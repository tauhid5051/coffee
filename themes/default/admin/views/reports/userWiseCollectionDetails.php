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

            <i class="fa-fw fa fa-user"></i>

            <?= $page_title ?>

        </h2>

        <div class="box-icon">

            <ul class="btn-tasks">

                <li>
                    <a href="<?= admin_url('reports/UserWiseCollection'); ?>"
                        class="tip"
                        title="Back to User Wise Collection">

                        <i class="icon fa fa-arrow-left"></i>

                    </a>
                </li>

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

                            <p style="font-size:18px;">

                                User:

                                <?= html_escape(
                                    trim(
                                        $user->first_name . ' ' .
                                            $user->last_name
                                    )
                                ) ?>

                            </p>

                            <p style="font-size:16px;">

                                Date Range:

                                <?= date(
                                    'd/m/Y',
                                    strtotime($start_date)
                                ) ?>

                                -

                                <?= date(
                                    'd/m/Y',
                                    strtotime($end_date)
                                ) ?>

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

                        <?= html_escape(
                            $this->session->userdata('username')
                        ); ?>

                    </div>

                </div>


                <br>


                <!-- =====================================================
                     DETAILS TABLE
                ====================================================== -->

                <div class="table-responsive">

                    <table id="userWiseCollectionDetailsTable"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        class="table table-bordered table-condensed table-hover table-striped reports-table"
                        style="width:100%;">

                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Collection</th>
                                <th>Collection By</th>
                            </tr>
                        </thead>

                        <tbody></tbody>

                        <tfoot>
                            <tr style="font-weight:bold;">
                                <th colspan="3" style="text-align:right;">
                                    TOTAL COLLECTION
                                </th>

                                <th style="text-align:right;">
                                    <?= number_format($totalCollection, 2) ?>
                                </th>

                                <th></th>
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

        $('#userWiseCollectionDetailsTable').DataTable({

            processing: true,

            serverSide: true,

            deferRender: true,

            pageLength: 25,

            lengthMenu: [
                [10, 25, 50, 100, 250],
                [10, 25, 50, 100, 250]
            ],

            searching: true,

            ordering: true,

            paging: true,

            info: true,

            searchDelay: 400,

            ajax: {
                url: '<?= admin_url("reports/UserWiseCollectionDetailsAjax"); ?>',

                type: 'POST',

                data: function(d) {

                    d.user = <?= (int) $user_id ?>;

                    d.start_date = '<?= $start_date ?>';

                    d.end_date = '<?= $end_date ?>';

                    // CI3 CSRF
                    d.<?= $this->security->get_csrf_token_name(); ?> =
                        '<?= $this->security->get_csrf_hash(); ?>';
                },

                dataSrc: function(json) {

                    // Refresh CI3 CSRF token
                    <?php if ($this->config->item('csrf_regenerate')): ?>

                        if (json.csrf_hash) {

                            $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]')
                                .val(json.csrf_hash);

                        }

                    <?php endif; ?>

                    return json.data;
                },

                error: function(xhr) {

                    console.error(
                        'Collection details AJAX error:',
                        xhr.responseText
                    );
                }
            },

            order: [
                [0, 'asc']
            ],

            columns: [

                {
                    data: 'time',
                    name: 'p.date'
                },

                {
                    data: 'invoice',
                    name: 's.reference_no'
                },

                {
                    data: 'customer',
                    name: 's.customer'
                },

                {
                    data: 'amount',
                    name: 'p.amount',
                    className: 'text-right'
                },

                {
                    data: 'paid_by',
                    name: 'p.paid_by'
                }

            ],

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

                {
                    extend: 'copyHtml5',

                    text: '<i class="fa fa-copy"></i> Copy',

                    className: 'btn btn-default btn-sm',

                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            page: 'current'
                        }
                    }
                },

                {
                    extend: 'excelHtml5',

                    text: '<i class="fa fa-file-excel-o"></i> Excel',

                    className: 'btn btn-success btn-sm',

                    title: 'User Wise Collection Details',

                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            page: 'current'
                        }
                    }
                },

                {
                    extend: 'csvHtml5',

                    text: '<i class="fa fa-file-text-o"></i> CSV',

                    className: 'btn btn-info btn-sm',

                    title: 'User Wise Collection Details',

                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            page: 'current'
                        }
                    }
                },

                {
                    extend: 'pdfHtml5',

                    text: '<i class="fa fa-file-pdf-o"></i> PDF',

                    className: 'btn btn-danger btn-sm',

                    orientation: 'landscape',

                    pageSize: 'A4',

                    title: 'User Wise Collection Details',

                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            page: 'current'
                        }
                    }
                },

                {
                    extend: 'print',

                    text: '<i class="fa fa-print"></i> Print',

                    className: 'btn btn-primary btn-sm',

                    title: 'User Wise Collection Details',

                    exportOptions: {
                        columns: ':visible',
                        modifier: {
                            page: 'current'
                        }
                    }
                }

            ]

        });

    });
</script>