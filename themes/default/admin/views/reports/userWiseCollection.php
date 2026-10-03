<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<link rel="stylesheet"
    href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


<div class="box">

    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="box-header">

        <h2 class="blue">
            <i class="fa-fw fa fa-users"></i>
            <?= $page_title ?>
        </h2>

        <div class="box-icon">

            <ul class="btn-tasks">

                <li>
                    <a href="javascript:void(0);"
                        class="tip"
                        onclick="window.print();"
                        title="<?= lang('print') ?>">
                        <i class="icon fa fa-print"></i>
                    </a>
                </li>

            </ul>

        </div>

    </div>


    <div class="box-content">

        <div class="row">

            <div class="col-lg-12">


                <!-- =====================================================
                     FILTER FORM
                ====================================================== -->

                <form id="saleFilterForm"
                    method="POST"
                    class="no-print">

                    <div class="row">


                        <!-- USER -->

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="select_user">
                                    User
                                </label>

                                <select name="user"
                                    id="select_user"
                                    class="form-control">

                                    <option value="">
                                        All Users
                                    </option>

                                    <?php if (!empty($allStaff)): ?>

                                        <?php foreach ($allStaff as $staff): ?>

                                            <?php
                                            $staff_id = isset($staff->id)
                                                ? $staff->id
                                                : (isset($staff['id']) ? $staff['id'] : '');

                                            $first_name = isset($staff->first_name)
                                                ? $staff->first_name
                                                : (isset($staff['first_name']) ? $staff['first_name'] : '');

                                            $last_name = isset($staff->last_name)
                                                ? $staff->last_name
                                                : (isset($staff['last_name']) ? $staff['last_name'] : '');

                                            $staff_name = trim($first_name . ' ' . $last_name);
                                            ?>

                                            <option value="<?= $staff_id ?>"
                                                <?= ($user_id == $staff_id) ? 'selected' : '' ?>>
                                                <?= html_escape($staff_name) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </select>

                            </div>

                        </div>


                        <!-- FROM DATE -->

                        <div class="col-md-3">

                            <div class="form-group">

                                <label for="start_date">
                                    From Date
                                </label>

                                <input type="text"
                                    name="start_date"
                                    id="start_date"
                                    class="form-control date"
                                    value="<?= !empty($start_date) ? date('d/m/Y', strtotime($start_date)) : date('d/m/Y') ?>">

                            </div>

                        </div>


                        <!-- TO DATE -->

                        <div class="col-md-3">

                            <div class="form-group">

                                <label for="end_date">
                                    To Date
                                </label>

                                <input type="text"
                                    name="end_date"
                                    id="end_date"
                                    class="form-control date"
                                    value="<?= !empty($end_date) ? date('d/m/Y', strtotime($end_date)) : date('d/m/Y') ?>">

                            </div>

                        </div>


                        <!-- SEARCH -->

                        <div class="col-md-2">

                            <div class="form-group">

                                <label>&nbsp;</label>

                                <button type="submit"
                                    class="btn btn-primary form-control">

                                    <i class="fa fa-search"></i>
                                    Search

                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- CSRF -->

                    <input type="hidden"
                        name="<?= $this->security->get_csrf_token_name(); ?>"
                        value="<?= $this->security->get_csrf_hash(); ?>">

                </form>


                <div class="clearfix"></div>

                <br>


                <!-- =====================================================
                     REPORT HEADER
                ====================================================== -->

                <div class="row">

                    <div class="col-xs-12">

                        <h2 style="
                            text-align:center;
                            font-weight:bold;
                            font-family:system-ui;
                        ">

                            <p style="font-size:20px;">
                                <?= $Settings->site_name ?>
                            </p>

                            <p>
                                <?= $page_title ?>
                            </p>

                            <p style="font-size:16px;">

                                Date Range:

                                <span id="reportDateRange"></span>

                            </p>

                        </h2>

                    </div>

                </div>


                <!-- =====================================================
                     PRINT INFORMATION
                ====================================================== -->

                <div class="row">

                    <div class="col-xs-6"
                        style="text-align:left;">

                        Print Date:
                        <?= date('d/m/Y h:i:sa'); ?>

                    </div>

                    <div class="col-xs-6"
                        style="text-align:right;">

                        Printed By:
                        <?= $this->session->userdata('username'); ?>

                    </div>

                </div>


                <br>


                <!-- =====================================================
                     SUMMARY TABLE
                ====================================================== -->

                <div class="table-responsive">

                    <table id="userWiseCollectionTable"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        class="table table-bordered table-condensed table-hover table-striped reports-table"
                        style="width:100%;">

                        <thead>

                            <tr>

                                <th>User</th>

                                <th>Invoices</th>

                                <th>Cash</th>

                                <th>CC</th>

                                <th>Cheque</th>

                                <th>Other</th>

                                <th>Total Collection</th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php if (!empty($records)): ?>

                                <?php foreach ($records as $row): ?>

                                    <tr>

                                        <td>

                                            <a href="javascript:void(0);"
                                                class="user-details"
                                                data-user-id="<?= (int) $row['user_id'] ?>">

                                                <i class="fa fa-user"></i>

                                                <?= html_escape($row['user_name']) ?>

                                            </a>

                                        </td>

                                        <td>
                                            <?= number_format(
                                                $row['total_invoice'],
                                                0
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= number_format(
                                                $row['cash'],
                                                2
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= number_format(
                                                $row['cc'],
                                                2
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= number_format(
                                                $row['cheque'],
                                                2
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= number_format(
                                                $row['other'],
                                                2
                                            ) ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?= number_format(
                                                    $row['total_collection'],
                                                    2
                                                ) ?>
                                            </strong>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>


                        <!-- =================================================
                             GRAND TOTAL
                        ================================================== -->

                        <tfoot>

                            <tr style="font-weight:bold;">

                                <th>
                                    TOTAL
                                </th>

                                <th>
                                    <?= number_format(
                                        $totalInvoice,
                                        0
                                    ) ?>
                                </th>

                                <th>
                                    <?= number_format(
                                        $totalCash,
                                        2
                                    ) ?>
                                </th>

                                <th>
                                    <?= number_format(
                                        $totalCC,
                                        2
                                    ) ?>
                                </th>

                                <th>
                                    <?= number_format(
                                        $totalCheque,
                                        2
                                    ) ?>
                                </th>

                                <th>
                                    <?= number_format(
                                        $totalOther,
                                        2
                                    ) ?>
                                </th>

                                <th>
                                    <?= number_format(
                                        $totalCollection,
                                        2
                                    ) ?>
                                </th>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
    $(document).ready(function() {


        // =====================================================
        // DATE RANGE
        // =====================================================

        function updateReportDateRange() {

            var start = $('#start_date').val();
            var end = $('#end_date').val();

            if (!start) {
                start = '<?= date('d/m/Y'); ?>';
            }

            if (!end) {
                end = '<?= date('d/m/Y'); ?>';
            }

            $('#reportDateRange').text(
                start + ' - ' + end
            );
        }


        updateReportDateRange();


        // =====================================================
        // DATATABLE
        // =====================================================

        var table = $('#userWiseCollectionTable').DataTable({

            pageLength: 25,

            lengthMenu: [
                [10, 25, 50, 100, 250, -1],
                [10, 25, 50, 100, 250, "All"]
            ],

            searching: true,

            ordering: true,

            paging: true,

            info: true,

            order: [
                [0, 'asc']
            ],


            // =================================================
            // BUTTONS
            // =================================================

            dom: '<"row no-print"' +
                '<"col-sm-4"l>' +
                '<"col-sm-4"B>' +
                '<"col-sm-4"f>' +
                '>' +
                'rt' +
                '<"row no-print"' +
                '<"col-sm-6"i>' +
                '<"col-sm-6"p>' +
                '>',


            buttons: [


                // =============================================
                // COPY
                // =============================================

                {
                    extend: 'copyHtml5',

                    text: '<i class="fa fa-copy"></i> Copy',

                    className: 'btn btn-default btn-sm',

                    exportOptions: {
                        columns: ':visible'
                    }
                },


                // =============================================
                // EXCEL
                // =============================================

                {
                    extend: 'excelHtml5',

                    text: '<i class="fa fa-file-excel-o"></i> Excel',

                    className: 'btn btn-success btn-sm',

                    title: 'User Wise Collection',

                    messageTop: '<?= $Settings->site_name ?>\n' +
                        'Date Range: ' +
                        $('#start_date').val() +
                        ' - ' +
                        $('#end_date').val(),

                    exportOptions: {
                        columns: ':visible'
                    }
                },


                // =============================================
                // CSV
                // =============================================

                {
                    extend: 'csvHtml5',

                    text: '<i class="fa fa-file-text-o"></i> CSV',

                    className: 'btn btn-info btn-sm',

                    title: 'User Wise Collection',

                    exportOptions: {
                        columns: ':visible'
                    }
                },


                // =============================================
                // PDF
                // =============================================

                {
                    extend: 'pdfHtml5',

                    text: '<i class="fa fa-file-pdf-o"></i> PDF',

                    className: 'btn btn-danger btn-sm',

                    orientation: 'landscape',

                    pageSize: 'A4',

                    title: 'User Wise Collection',

                    exportOptions: {
                        columns: ':visible'
                    }
                },


                // =============================================
                // PRINT
                // =============================================

                {
                    extend: 'print',

                    text: '<i class="fa fa-print"></i> Print',

                    className: 'btn btn-primary btn-sm',

                    title: 'User Wise Collection',

                    messageTop: function() {

                        return (
                            '<?= $Settings->site_name ?>' +
                            '<br>' +
                            'Date Range: ' +
                            $('#start_date').val() +
                            ' - ' +
                            $('#end_date').val()
                        );

                    },

                    exportOptions: {
                        columns: ':visible'
                    }
                }

            ]

        });


        // =====================================================
        // FILTER SUBMIT
        // =====================================================

        $('#saleFilterForm').on('submit', function(e) {

            e.preventDefault();

            updateReportDateRange();

            /*
             * Part 1 currently loads the report through
             * normal controller POST.
             *
             * Therefore reload the page with POST-compatible
             * form submission rather than AJAX.
             */

            this.submit();

        });


        // =====================================================
        // USER CLICK
        // =====================================================

        $(document).on(
            'click',
            '.user-details',
            function(e) {

                e.preventDefault();

                var userId = $(this).data('user-id');

                var startDate = $('#start_date').val() || '';
                var endDate = $('#end_date').val() || '';

                if (!userId) {
                    return;
                }

                var url =
                    '<?= admin_url("reports/UserWiseCollectionDetails"); ?>' +
                    '?user=' + encodeURIComponent(userId) +
                    '&start_date=' + encodeURIComponent(startDate) +
                    '&end_date=' + encodeURIComponent(endDate);

                window.location.href = url;
            }
        );


    });
</script>