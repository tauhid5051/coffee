<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<link rel="stylesheet"
    href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

<!-- Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Export buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>


<div class="box">
    <div class="box-header">
        <h2 class="blue">
            <i class="fa-fw fa fa-cubes"></i>
            <?= $page_title ?>
        </h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a id="print333"
                        class="tip"
                        onclick="window.print();"
                        title="<?= lang('print') ?>">
                        <i class="icon fa fa-print"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#"
                        id="image"
                        class="tip"
                        title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <div id="form" class="no-print">
                    <?php echo admin_form_open(
                        'reports/itemstock',
                        'autocomplete="off"'
                    ); ?>
                    <div class="row">
                        <!-- Product -->
                        <div class="col-xs-12 col-sm-3">
                            <div class="form-group">
                                <?= lang('product', 'product') ?>
                                <?php
                                $pro = [
                                    '' => lang('select') . ' ' . lang('product')
                                ];
                                foreach ($allproducts as $product) {

                                    $pro[$product->id] = $product->name;
                                }

                                echo form_dropdown(
                                    'product',
                                    $pro,
                                    ($_POST['product'] ?? ''),
                                    'class="form-control select"
                                     id="select_product"
                                     placeholder="' . lang('select') . ' ' . lang('product') . '"
                                     style="width:100%"'
                                );

                                ?>

                            </div>

                        </div>


                        <!-- Category -->
                        <div class="col-xs-12 col-sm-3">

                            <div class="form-group">

                                <?= lang('category', 'category') ?>

                                <?php

                                $cat = [
                                    '' => lang('select') . ' ' . lang('category')
                                ];

                                foreach ($categories as $category) {

                                    $cat[$category->id] = $category->name;
                                }

                                echo form_dropdown(
                                    'category',
                                    $cat,
                                    ($_POST['category'] ?? ''),
                                    'class="form-control select"
                                     id="category"
                                     placeholder="' . lang('select') . ' ' . lang('category') . '"
                                     style="width:100%"'
                                );

                                ?>

                            </div>

                        </div>


                        <!-- Date Upto -->
                        <div class="col-xs-12 col-sm-3">
                            <div class="form-group">

                                <?= lang('Date Upto', 'end_date'); ?>

                                <?php
                                echo form_input(
                                    'end_date',
                                    $_POST['end_date'] ?? '',
                                    'class="form-control date"
                                    id="end_date"
                                    required'
                                );
                                ?>

                            </div>
                        </div>



                        <!-- Stock From -->
                        <div class="col-xs-6 col-sm-1">

                            <div class="form-group">

                                <?= lang(
                                    'Stock qty from',
                                    'stock_from'
                                ); ?>

                                <input type="number"
                                    name="stock_from"
                                    id="stock_from"
                                    class="form-control"
                                    step="1"
                                    value="<?= isset($_POST['stock_from'])
                                                ? html_escape($_POST['stock_from'])
                                                : '' ?>">

                            </div>

                        </div>


                        <!-- Stock To -->
                        <div class="col-xs-6 col-sm-1">

                            <div class="form-group">

                                <?= lang(
                                    'Stock qty to',
                                    'stock_to'
                                ); ?>

                                <input type="number"
                                    name="stock_to"
                                    id="stock_to"
                                    class="form-control"
                                    step="1"
                                    value="<?= isset($_POST['stock_to'])
                                                ? html_escape($_POST['stock_to'])
                                                : '' ?>">

                            </div>

                        </div>

                    </div>


                    <!-- Submit Button -->
                    <div class="row">

                        <div class="col-xs-6 col-sm-1">

                            <div class="form-group">

                                <?php

                                echo form_submit(
                                    'submit_report',
                                    $this->lang->line('submit'),
                                    'class="btn btn-primary btn-block"'
                                );

                                ?>

                            </div>

                        </div>

                    </div>

                    <?php echo form_close(); ?>

                </div>


                <div class="clearfix"></div>

                <br>


                <!-- =========================================
                     REPORT HEADER
                ========================================== -->

                <div class="row">

                    <div class="col-xs-12 text-center">

                        <h2 style="
                            font-weight: bold;
                            font-family: system-ui;
                            margin-bottom: 5px;
                        ">

                            <p style="
                                font-size: 20px;
                                margin: 0;
                            ">
                                <?= $Settings->site_name ?>
                            </p>

                            <p style="
                                font-size: 16px;
                                margin: 5px 0;
                            ">
                                <?= $page_title ?>
                            </p>

                            <p style="
                                font-size: 14px;
                                margin: 5px 0;
                            ">
                                Date Upto:
                                <?= $date_range ?? ''; ?>
                            </p>

                        </h2>

                    </div>

                </div>


                <!-- Print Information -->
                <div class="row"
                    style="margin-bottom: 15px;">

                    <div class="col-xs-6"
                        style="text-align: left;">

                        <small>
                            Print Date:
                            <?= date('d/m/y h:i:sa'); ?>
                        </small>

                    </div>

                    <div class="col-xs-6"
                        style="text-align: right;">

                        <small>
                            Printed By:
                            <?= $this->session->userdata('username'); ?>
                        </small>

                    </div>

                </div>

                <!-- /Report Header -->

                <!-- =========================================
                     STOCK REPORT TABLE
                ========================================== -->

                <div class="table-responsive">

                    <table id="CusData5"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        class="table table-bordered table-condensed table-hover table-striped reports-table"
                        style="width:100%">

                        <!-- =================================
                             TABLE HEADER
                        ================================== -->

                        <thead>

                            <tr class="primary">

                                <th style="text-align: left;">
                                    <?= lang('ID'); ?>
                                </th>

                                <th style="text-align: left;">
                                    <?= lang('Code'); ?>
                                </th>

                                <th style="text-align: left;">
                                    <?= lang('Category'); ?>
                                </th>

                                <th style="text-align: left;">
                                    <?= lang('Name'); ?>
                                </th>

                                <th style="text-align: right;">
                                    <?= lang('Purchase'); ?>
                                </th>

                                <th style="text-align: right;">
                                    <?= lang('Sale'); ?>
                                </th>

                                <th style="text-align: right;">
                                    <?= lang('Adjustment'); ?>
                                </th>

                                <th style="text-align: right;">
                                    <?= lang('Stock'); ?>
                                </th>

                            </tr>

                        </thead>


                        <!-- =================================
                             TABLE BODY
                        ================================== -->

                        <tbody>

                            <?php if (!empty($records)) : ?>

                                <?php foreach ($records as $item) : ?>

                                    <?php

                                    $purchase = intval($item['purchase']);
                                    $sale     = intval($item['sale']);
                                    $adjust   = intval($item['adjust']);

                                    $stock = $purchase + $adjust - $sale;

                                    ?>

                                    <tr>

                                        <td style="text-align: left;">
                                            <?= html_escape($item['product_id']); ?>
                                        </td>

                                        <td style="text-align: left;">
                                            <?= html_escape($item['code']); ?>
                                        </td>

                                        <td style="text-align: left;">
                                            <?= html_escape($item['category_name']); ?>
                                        </td>

                                        <td style="text-align: left;">
                                            <?= html_escape($item['name']); ?>
                                        </td>

                                        <td style="text-align: right;">
                                            <?= $purchase; ?>
                                        </td>

                                        <td style="text-align: right;">
                                            <?= $sale; ?>
                                        </td>

                                        <td style="text-align: right;">
                                            <?= $adjust; ?>
                                        </td>

                                        <td style="text-align: right;">
                                            <?= $stock; ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>



                        <!-- =================================
                             TABLE FOOTER / TOTAL
                        ================================== -->


                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================================
     HTML2CANVAS
========================================== -->

<script type="text/javascript"
    src="<?= $assets ?>js/html2canvas.min.js">
</script>
<script type="text/javascript">
    $(document).ready(function() {


        $('form').on('submit', function(e) {

            var endDate = $.trim($('#end_date').val());

            if (endDate === '') {

                e.preventDefault();

                alert('Please select Date Upto.');

                $('#end_date').focus();

                return false;
            }

        });



        /* ======================================================
           IMAGE EXPORT
        ====================================================== */

        $('#image').click(function(event) {

            event.preventDefault();

            html2canvas($('.box'), {

                onrendered: function(canvas) {

                    openImg(canvas.toDataURL());

                }

            });

            return false;

        });


        /* ======================================================
           DATATABLE
        ====================================================== */

        $('#CusData5').DataTable({

            pageLength: 25,

            lengthMenu: [
                [10, 25, 50, 100, 250, -1],
                [10, 25, 50, 100, 250, "All"]
            ],

            searching: true,

            ordering: true,

            paging: true,

            info: true,

            order: [],

            columnDefs: [

                {
                    targets: [0],
                    type: 'num'
                },

                {
                    targets: [4, 5, 6, 7],
                    type: 'num'
                }

            ],


            /* ==================================================
               DATATABLE LAYOUT
            ================================================== */

            dom: '<"row no-print"' +

                '<"col-sm-4"l>' +

                '<"col-sm-8 text-right"Bf>' +

                '>' +

                'rt' +

                '<"row no-print"' +

                '<"col-sm-6"i>' +

                '<"col-sm-6 text-right"p>' +

                '>',


            /* ==================================================
               EXPORT BUTTONS
            ================================================== */

            buttons: [

                /* ------------------------------------------------
                   COPY
                ------------------------------------------------ */

                {
                    extend: 'copy',

                    text: '<i class="fa fa-copy"></i> Copy',

                    className: 'btn btn-default btn-sm',

                    title: '<?= $Settings->site_name ?> - <?= $page_title ?>',

                    footer: true,

                    exportOptions: {
                        columns: ':visible'
                    }

                },


                /* ------------------------------------------------
                   SAVE AS EXCEL
                ------------------------------------------------ */

                {
                    extend: 'excelHtml5',

                    text: '<i class="fa fa-file-excel-o"></i> Save as Excel',

                    className: 'btn btn-success btn-sm',

                    title: '<?= $Settings->site_name ?> - <?= $page_title ?>',

                    messageTop: 'Date Upto: <?= $date_range ?? ''; ?>',

                    footer: true,

                    filename: 'Stock_Report_<?= date("Y-m-d"); ?>',

                    exportOptions: {
                        columns: ':visible'
                    }

                },


                /* ------------------------------------------------
                   CSV
                ------------------------------------------------ */

                {
                    extend: 'csvHtml5',

                    text: '<i class="fa fa-file-text-o"></i> CSV',

                    className: 'btn btn-info btn-sm',

                    title: '<?= $Settings->site_name ?> - <?= $page_title ?>',

                    footer: true,

                    filename: 'Stock_Report_<?= date("Y-m-d"); ?>',

                    exportOptions: {
                        columns: ':visible'
                    }

                },


                /* ------------------------------------------------
                   SAVE AS PDF
                ------------------------------------------------ */

                {
                    extend: 'pdfHtml5',

                    text: '<i class="fa fa-file-pdf-o"></i> Save as PDF',

                    className: 'btn btn-danger btn-sm',

                    title: '<?= $Settings->site_name ?>',

                    messageTop: '<?= $page_title ?>' +
                        '\nDate Upto: <?= $date_range ?? ''; ?>',

                    footer: true,

                    filename: 'Stock_Report_<?= date("Y-m-d"); ?>',

                    orientation: 'landscape',

                    pageSize: 'A4',

                    exportOptions: {
                        columns: ':visible'
                    },

                    customize: function(doc) {

                        /*
                         * Page margins
                         */
                        doc.pageMargins = [
                            20,
                            30,
                            20,
                            30
                        ];


                        /*
                         * Default font size
                         */
                        doc.defaultStyle.fontSize = 8;


                        /*
                         * Header font size
                         */
                        if (doc.styles.tableHeader) {

                            doc.styles.tableHeader.fontSize = 8;

                            doc.styles.tableHeader.bold = true;

                        }


                        /*
                         * Make table use full page
                         */
                        if (
                            doc.content &&
                            doc.content.length
                        ) {

                            doc.content.forEach(function(item) {

                                if (
                                    item.table &&
                                    item.table.body
                                ) {

                                    item.table.widths =
                                        Array(
                                            item.table.body[0].length
                                        ).fill('*');

                                }

                            });

                        }

                    }

                },


                /* ------------------------------------------------
                   PRINT
                ------------------------------------------------ */

                {
                    extend: 'print',

                    text: '<i class="fa fa-print"></i> Print',

                    className: 'btn btn-primary btn-sm',

                    title: '<?= $Settings->site_name ?>',

                    messageTop: '<h3 style="text-align:center">' +
                        '<?= $page_title ?>' +
                        '</h3>' +

                        '<p style="text-align:center">' +
                        'Date Upto: <?= $date_range ?? ''; ?>' +
                        '</p>',

                    footer: true,

                    exportOptions: {
                        columns: ':visible'
                    }

                }

            ],


            /* ==================================================
               LANGUAGE
            ================================================== */

            language: {

                search: "Search:",

                searchPlaceholder: "Search product...",

                lengthMenu: "Show _MENU_ entries",

                info: "Showing _START_ to _END_ of _TOTAL_ entries",

                infoEmpty: "Showing 0 to 0 of 0 entries",

                zeroRecords: "No matching records found",

                emptyTable: "No data available",

                paginate: {

                    first: "First",

                    last: "Last",

                    next: "Next",

                    previous: "Previous"

                }

            }

        });

    });
</script>


<style type="text/css">
    /* DataTables top area */

    #CusData5_wrapper .dataTables_length {
        margin-bottom: 10px;
    }

    #CusData5_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }


    /* Buttons */

    #CusData5_wrapper .dt-buttons {
        display: inline-block;
        margin-right: 5px;
    }

    #CusData5_wrapper .dt-button {
        margin-left: 3px;
    }


    /* Footer */

    #CusData5 tfoot th {
        font-weight: bold;
        background-color: #f5f5f5;
    }


    /* Numeric columns */

    #CusData5 th:nth-child(n+5),
    #CusData5 td:nth-child(n+5) {
        text-align: right;
    }


    /* Print */

    @media print {

        .no-print,
        .dataTables_length,
        .dataTables_filter,
        .dt-buttons,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }

        #CusData5 {
            width: 100% !important;
        }

        #CusData5 tfoot {
            display: table-row-group;
        }

    }
</style>
<style type="text/css">
    /* ----------------------------------------------------------
   DataTables Length
     ----------------------------------------------------------- */

    #CusData5_wrapper .dataTables_length {

        margin-bottom: 10px;

    }


    /* ----------------------------------------------------------
   DataTables Search
    ----------------------------------------------------------- */

    #CusData5_wrapper .dataTables_filter {

        margin-bottom: 10px;

    }


    /* ----------------------------------------------------------
   Buttons
    ----------------------------------------------------------- */

    #CusData5_wrapper .dt-buttons {

        display: inline-block;

        margin-right: 5px;

    }


    #CusData5_wrapper .dt-button {

        margin-left: 3px;

    }


    /* ----------------------------------------------------------
   Footer Total
    ----------------------------------------------------------- */

    #CusData5 tfoot th {

        font-weight: bold;

        background-color: #f5f5f5;

    }


    /* ----------------------------------------------------------
   Numeric columns
    ----------------------------------------------------------- */

    #CusData5 th:nth-child(n+5),
    #CusData5 td:nth-child(n+5) {

        text-align: right;

    }


    /* ----------------------------------------------------------
   Table wrapper
    ----------------------------------------------------------- */

    .dataTables_wrapper {

        width: 100%;

    }


    /* ----------------------------------------------------------
   Print
     ----------------------------------------------------------- */

    @media print {

        .no-print,

        .dataTables_length,

        .dataTables_filter,

        .dt-buttons,

        .dataTables_info,

        .dataTables_paginate {

            display: none !important;

        }


        #CusData5 {

            width: 100% !important;

        }


        #CusData5 tfoot {

            display: table-row-group;

        }


        #CusData5 th,
        #CusData5 td {

            font-size: 11px;

        }

    }
</style>