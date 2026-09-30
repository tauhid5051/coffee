<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mis_reports extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('reports_model');
        $this->load->admin_model('products_model');
        $this->load->admin_model('companies_model');
        $this->load->helper('pos');

        $this->data['pb'] = [
            'cash'       => lang('cash'),
            'CC'         => lang('CC'),
            'Cheque'     => lang('Cheque'),
            'paypal_pro' => lang('paypal_pro'),
            'stripe'     => lang('stripe'),
            'gift_card'  => lang('gift_card'),
            'deposit'    => lang('deposit'),
            'authorize'  => lang('authorize'),
        ];
    }

    public function index()
    {
        try {
            $data['error']               = validation_errors() ?: $this->session->flashdata('error');
            $this->data['monthly_sales'] = $this->reports_model->getChartData();
            $this->data['stock']         = $this->reports_model->getStockValue();
            $bc                          = [
                ['link' => base_url(), 'page' => lang('home')],
                ['link' => '#', 'page' => lang('reports')],
            ];
            $meta = ['page_title' => lang('reports'), 'bc' => $bc];
            $this->page_construct('mis_reports/index', $meta, $this->data);
        } catch (\Throwable $e) {
            log_message('error', 'Mis_reports::index failed: ' . $e->getMessage());
            $this->session->set_flashdata('error', lang('error_loading_reports') ?: 'Unable to load reports page.');
            redirect(base_url());
        }
    }

    public function openModal($type = null, $subtype = null)
    {
        try {
            $allowed = ['paymentSummeryReport', 'paymentSummaryDayReport'];
            if (!in_array($type, $allowed, true)) {
                log_message('error', 'openModal called with invalid type: ' . print_r($type, true));
                show_404();
                return;
            }

            $this->data['error']     = validation_errors() ?: $this->session->flashdata('error');
            $this->data['users']     = $this->reports_model->getStaff();
            $this->data['user_id']   = null;
            $this->data['warehouse'] = $this->site->getWarehouseByID(
                $this->Settings->default_warehouse ?: 1
            );
            $this->data['modal_js']  = $this->site->modal_js();
            $this->data['type']      = $type;
            $this->data['subtype']   = $subtype;

            switch ($type) {
                case 'paymentSummeryReport':
                    $this->data['reportType'] = 'User Wise Collection';
                    $this->data['subReport']  = true;
                    break;

                case 'paymentSummaryDayReport':
                    $this->data['reportType'] = 'Date Wise Collection';
                    $this->data['subReport']  = false;
                    break;
            }

            $this->load->view($this->theme . 'mis_reports/report_modals', $this->data);
        } catch (\Throwable $e) {
            log_message('error', 'Mis_reports::openModal failed: ' . $e->getMessage());
            show_error('Unable to open report dialog.', 500);
        }
    }

    public function createJasper()
    {
        try {
            $name    = $this->input->post('type', true);
            $format  = strtolower((string) $this->input->post('format', true));
            if (!$format || $format === 'pdf') {
                $format = 'pdf';
            }

            $allowed = ['paymentSummeryReport', 'paymentSummaryDayReport'];
            if (empty($name) || !in_array($name, $allowed, true)) {
                $this->_respondError('Invalid or missing report type.', 400);
                return;
            }

            $startDate = $this->input->post('startDate', true);
            $endDate   = $this->input->post('endDate', true);

            if (empty($startDate) || empty($endDate)) {
                $this->_respondError('Start date and end date are required.', 400);
                return;
            }

            if (strtotime($startDate) === false || strtotime($endDate) === false) {
                $this->_respondError('Start date / end date is not a valid date.', 400);
                return;
            }

            if (strtotime($startDate) > strtotime($endDate)) {
                $this->_respondError('Start date cannot be after end date.', 400);
                return;
            }

            $createdBy = $this->input->post('createdBy', true) ?: 'All';
            $address   = trim(strip_tags((string) $this->input->post('branchAddress', true)));
            $branchName = $this->data['Settings']->site_name ?? '';

            if ($name === 'paymentSummeryReport') {
                $this->_renderPaymentSummaryReport($startDate, $endDate, $createdBy, $address, $branchName);
                return;
            }

            if ($name === 'paymentSummaryDayReport') {
                $this->_renderPaymentSummaryDayReport($startDate, $endDate, $createdBy, $address, $branchName);
                return;
            }
        } catch (\Throwable $e) {
            log_message('error', 'Mis_reports::createJasper failed: ' . $e->getMessage());
            $this->_respondError('An unexpected error occurred while generating the report.', 500);
        }
    }

    private function _renderPaymentSummaryReport($startDate, $endDate, $createdBy, $address, $branchName)
    {
        $sql = "SELECT b.created_by, CONCAT(TRIM(sma_users.first_name),' ',TRIM(sma_users.last_name)) NAME,
                SUM(b.total) total, SUM(b.grand_total) grand_total, SUM(b.total_bill) total_bill,
                SUM(b.total_discount) total_discount, SUM(b.total_adjust) total_adjust,
                SUM(b.total_shipping) total_shipping, SUM(b.total_received) total_received
            FROM (
                SELECT a.*, 0 total_received FROM (
                    SELECT SUM(total) total, SUM(grand_total) AS grand_total, COUNT(id) AS total_bill,
                        SUM(order_discount) AS total_discount, SUM(adjust) AS total_adjust,
                        SUM(shipping) AS total_shipping, created_by
                    FROM sma_sales
                    WHERE DATE(date) BETWEEN ? AND ?
                        AND created_by = (CASE WHEN ?='All' THEN created_by ELSE ? END)
                    GROUP BY created_by
                ) AS a INNER JOIN sma_users ON a.created_by = sma_users.id
                UNION ALL
                SELECT 0 total, 0 grand_total, 0 total_bill, 0 total_discount, 0 total_adjust,
                    0 total_shipping, created_by, SUM(amount) AS total_received
                FROM sma_payments
                WHERE DATE(date) BETWEEN ? AND ?
                    AND created_by = (CASE WHEN ?='All' THEN created_by ELSE ? END)
                GROUP BY created_by
            ) b, sma_users WHERE sma_users.id = b.created_by
            GROUP BY b.created_by
            ORDER BY CONCAT(sma_users.first_name, sma_users.last_name) ASC";

        $q = $this->db->query($sql, [$startDate, $endDate, $createdBy, $createdBy, $startDate, $endDate, $createdBy, $createdBy]);
        $rows = $q->result();

        $subSql = "SELECT CONCAT(sma_users.first_name,' ',COALESCE(sma_users.last_name,'')) user_name, p.paid_by, SUM(p.amount) AS total_received
            FROM sma_payments p
            LEFT JOIN sma_users ON sma_users.id = p.created_by
            WHERE DATE(p.date) BETWEEN ? AND ?
                AND p.created_by = (CASE WHEN ?='All' THEN p.created_by ELSE ? END)
            GROUP BY p.created_by, p.paid_by";

        $subQ = $this->db->query($subSql, [$startDate, $endDate, $createdBy, $createdBy]);
        $subRows = $subQ->result();

        $paidByTypes = [];
        foreach ($subRows as $sub) {
            if (!in_array($sub->paid_by, $paidByTypes, true)) {
                $paidByTypes[] = $sub->paid_by;
            }
        }
        sort($paidByTypes);

        $html = '<html><head><meta charset="UTF-8"><style>
            body { font-family: sans-serif; font-size: 10pt; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 0.5px solid #000; padding: 4px; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .bold { font-weight: bold; }
            .bg-black { background-color: #000; color: #fff; }
            .bg-blue { background-color: #F0F8FF; }
            .bg-total { background-color: #BFE1FF; }
            .title { font-size: 14pt; font-weight: bold; text-align: center; margin-bottom: 4px; }
            .subtitle { font-size: 12pt; font-weight: bold; text-align: center; margin-bottom: 8px; }
            .meta { font-size: 9pt; margin-bottom: 12px; }
            .section-title { font-size: 11pt; font-weight: bold; margin-top: 12px; margin-bottom: 6px; text-align: center; }
        </style></head><body>';

        $html .= '<div class="title">Payment Summary Report</div>';
        $html .= '<div class="subtitle">' . htmlspecialchars($branchName, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="meta">Date Range: ' . htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8') . ' to ' . htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="meta">Print Date: ' . date('d/M/Y h:i:s a') . '</div>';

        $html .= '<table>';
        $html .= '<thead><tr>';
        $html .= '<th style="width:22%;">Name</th>';
        $html .= '<th style="width:10%; text-align:right;">Bill Quantity</th>';
        $html .= '<th style="width:12%; text-align:right;">Total Amount</th>';
        $html .= '<th style="width:10%; text-align:right;">Discount</th>';
        $html .= '<th style="width:12%; text-align:right;">Payable</th>';
        $html .= '<th style="width:12%; text-align:right;">Shipping</th>';
        $html .= '<th style="width:12%; text-align:right;">Collection</th>';
        $html .= '</tr></thead><tbody>';

        $totalBillNo = 0;
        $totalBillAmount = 0;
        $totalDiscount = 0;
        $totalPayable = 0;
        $totalShipping = 0;
        $totalCollection = 0;
        $rowNo = 0;

        foreach ($rows as $row) {
            $rowNo++;
            $totalBillNo += (int) $row->total_bill;
            $totalBillAmount += (float) $row->total;
            $totalDiscount += (float) $row->total_discount;
            $totalPayable += (float) $row->grand_total;
            $totalShipping += (float) $row->total_shipping;
            $totalCollection += (float) $row->total_received;

            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row->NAME, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td class="text-right">' . number_format((int) $row->total_bill, 0, '.', ',') . '</td>';
            $html .= '<td class="text-right">' . number_format((float) $row->total, 2, '.', ',') . '</td>';
            $html .= '<td class="text-right">' . number_format((float) $row->total_discount, 2, '.', ',') . '</td>';
            $html .= '<td class="text-right">' . number_format((float) $row->grand_total, 2, '.', ',') . '</td>';
            $html .= '<td class="text-right">' . number_format((float) $row->total_shipping, 2, '.', ',') . '</td>';
            $html .= '<td class="text-right">' . number_format((float) $row->total_received, 2, '.', ',') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody><tfoot>';
        $html .= '<tr class="bg-total">';
        $html .= '<td class="bold">Total</td>';
        $html .= '<td class="text-right bold">' . number_format($totalBillNo, 0, '.', ',') . '</td>';
        $html .= '<td class="text-right bold">' . number_format($totalBillAmount, 2, '.', ',') . '</td>';
        $html .= '<td class="text-right bold">' . number_format($totalDiscount, 2, '.', ',') . '</td>';
        $html .= '<td class="text-right bold">' . number_format($totalPayable, 2, '.', ',') . '</td>';
        $html .= '<td class="text-right bold">' . number_format($totalShipping, 2, '.', ',') . '</td>';
        $html .= '<td class="text-right bold">' . number_format($totalCollection, 2, '.', ',') . '</td>';
        $html .= '</tr>';
        $html .= '<tr class="bg-total">';
        $html .= '<td class="bold"></td>';
        $html .= '<td class="text-right bold">' . number_format($rowNo, 0, '.', ',') . '</td>';
        $html .= '<td colspan="5"></td>';
        $html .= '</tr>';
        $html .= '</tfoot></table>';

        if (!empty($subRows)) {
            $html .= '<div class="section-title">Collection Break Down</div>';
            $html .= '<table>';
            $html .= '<thead><tr>';
            $html .= '<th style="width:20%;">Name / Payment Type</th>';
            foreach ($paidByTypes as $pt) {
                $html .= '<th style="width:12%; text-align:right;">' . htmlspecialchars($pt, ENT_QUOTES, 'UTF-8') . '</th>';
            }
            $html .= '<th style="width:12%; text-align:right;">Total</th>';
            $html .= '</tr></thead><tbody>';

            $userTotals = [];
            $colTotals = array_fill_keys($paidByTypes, 0);
            $grandTotal = 0;

            foreach ($subRows as $sub) {
                $userTotals[$sub->user_name][$sub->paid_by] = (float) $sub->total_received;
                $colTotals[$sub->paid_by] += (float) $sub->total_received;
                $grandTotal += (float) $sub->total_received;
            }

            foreach ($userTotals as $user => $vals) {
                $html .= '<tr class="bg-blue">';
                $html .= '<td>' . htmlspecialchars($user, ENT_QUOTES, 'UTF-8') . '</td>';
                $rowTotal = 0;
                foreach ($paidByTypes as $pt) {
                    $val = $vals[$pt] ?? 0;
                    $rowTotal += $val;
                    $html .= '<td class="text-right">' . number_format($val, 0, '.', ',') . '</td>';
                }
                $html .= '<td class="text-right bold">' . number_format($rowTotal, 0, '.', ',') . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr class="bg-total">';
            $html .= '<td class="bold">Total</td>';
            foreach ($paidByTypes as $pt) {
                $html .= '<td class="text-right bold">' . number_format($colTotals[$pt], 0, '.', ',') . '</td>';
            }
            $html .= '<td class="text-right bold">' . number_format($grandTotal, 0, '.', ',') . '</td>';
            $html .= '</tr>';

            $html .= '</tbody></table>';
        }

        $html .= '</body></html>';

        $name = 'paymentSummeryReport_' . date('Y-m-d') . '.pdf';
        $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'P');
    }

    private function _renderPaymentSummaryDayReport($startDate, $endDate, $createdBy, $address, $branchName)
    {
        $sql = "SELECT DATE(sma_payments.Date) Date, SUM(amount) PathologyPaid,
                0 ServicePaid, SUM(amount) TotalAmount
            FROM sma_sales
            JOIN sma_payments ON sma_sales.id = sma_payments.sale_id
            JOIN sma_users ON sma_payments.created_by = sma_users.id
            WHERE DATE(sma_payments.Date) BETWEEN ? AND ?
                AND sma_payments.created_by = (CASE WHEN ?='all' THEN sma_users.id ELSE ? END)
            GROUP BY DATE(sma_payments.Date)";

        $q = $this->db->query($sql, [$startDate, $endDate, strtolower($createdBy), $createdBy]);
        $rows = $q->result();

        $html = '<html><head><meta charset="UTF-8"><style>
            body { font-family: sans-serif; font-size: 10pt; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 0.5px solid #000; padding: 4px; }
            .text-right { text-align: right; }
            .bold { font-weight: bold; }
            .bg-black { background-color: #000; color: #fff; }
            .bg-total { background-color: #BFE1FF; }
            .title { font-size: 14pt; font-weight: bold; text-align: center; margin-bottom: 4px; }
            .subtitle { font-size: 12pt; font-weight: bold; text-align: center; margin-bottom: 8px; }
            .meta { font-size: 9pt; margin-bottom: 12px; }
        </style></head><body>';

        $html .= '<div class="title">Payment Summary Report (Date Wise)</div>';
        $html .= '<div class="subtitle">' . htmlspecialchars($branchName, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="meta">Date Range: ' . htmlspecialchars($startDate, ENT_QUOTES, 'UTF-8') . ' to ' . htmlspecialchars($endDate, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="meta">Print Date: ' . date('d/M/Y h:i:s a') . '</div>';

        $html .= '<table>';
        $html .= '<thead><tr>';
        $html .= '<th style="width:20%;">Date</th>';
        $html .= '<th style="width:40%; text-align:right;">Total</th>';
        $html .= '<th style="width:40%; text-align:right;">Comments</th>';
        $html .= '</tr></thead><tbody>';

        $totalCollection = 0;
        $rowNo = 0;

        foreach ($rows as $row) {
            $rowNo++;
            $totalCollection += (float) $row->TotalAmount;
            $html .= '<tr>';
            $html .= '<td>' . date('d M Y', strtotime($row->Date)) . '</td>';
            $html .= '<td class="text-right">' . number_format((float) $row->TotalAmount, 2, '.', ',') . '</td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody><tfoot>';
        $html .= '<tr class="bg-total">';
        $html .= '<td class="bold">Total</td>';
        $html .= '<td class="text-right bold">' . number_format($totalCollection, 2, '.', ',') . '</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        $html .= '<tr class="bg-total">';
        $html .= '<td class="bold"></td>';
        $html .= '<td class="text-right bold">' . number_format($rowNo, 0, '.', ',') . '</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        $html .= '</tfoot></table>';

        $html .= '</body></html>';

        $name = 'paymentSummaryDayReport_' . date('Y-m-d') . '.pdf';
        $this->sma->generate_pdf($html, $name, null, null, null, null, null, 'P');
    }

    private function _respondError($message, $statusCode = 400)
    {
        if ($this->input->is_ajax_request()) {
            $this->output
                ->set_status_header($statusCode)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'error'   => $message,
                ]));
            return;
        }

        show_error($message, $statusCode);
    }
}
