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
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= $page_title ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <ul class="btn-tasks">
                    <li class="dropdown"><a id="print333" class="tip" onclick="window.print();" title="<?= lang('print') ?>"><i class="icon fa fa-print"></i></a></li>
                    <li class="dropdown"><a href="#" id="image" class="tip" title="<?= lang('save_image') ?>"><i class="icon fa fa-file-picture-o"></i></a></li>
                </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang(' '); ?></p>
                <!-- pppp -->
                <div id="form" class="no-print" style=" max-width: 800px; ">
                    <form id="purchaseFilterForm" autocomplete="off">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <?= lang('item_id', 'item_id'); ?>
                                    <?php echo form_input('item_id', (isset($_POST['item_id']) ? $_POST['item_id'] : ''), 'class="form-control number"   id="item_id"'); ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <?= lang('item_name', 'item_name'); ?>
                                    <?php echo form_input('item_name', (isset($_POST['item_name']) ? $_POST['item_name'] : ''), 'class="form-control number"   id="item_name"'); ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <?= lang('start_date', 'start_date'); ?>
                                    <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ''), 'class="form-control date"   id="start_date"'); ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <?= lang('end_date', 'end_date'); ?>
                                    <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ''), 'class="form-control date"  id="end_date"'); ?>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <div class="controls">
                                <button type="submit" class="btn btn-primary">
                                    <?= $this->lang->line('submit'); ?>
                                </button>

                            </div>
                        </div>
                        <?php echo form_close(); ?>
                </div>
                <div class="clearfix"></div>
                <br>
                <!-- /ppppppp -->
                <div class="row">
                    <h2 style="text-align: center;font-weight: bold;font-family: system-ui;">
                        <p style="font-size: 20px;"> <?= $Settings->site_name ?> </p>
                        <!-- <br> -->
                        <p> <?= $page_title ?> </p>
                        <!-- <br> -->
                        <p>
                            Date Range:
                        </p>
                    </h2>
                </div>
                <div class="row col-xs-12">
                    <div class="col-xs-6" style=" text-align: left; ">
                        Print Date: <?= date('d/m/y h:i:sa'); ?>
                    </div>
                    <div class="col-xs-6" style=" text-align: end; ">
                        Printed By: <?= $this->session->userdata('username'); ?>
                    </div>
                </div>
                <!-- /ppppppp -->
                <div class="table-responsive">
                    <table id="PurchaseData"
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        class="table table-bordered table-condensed table-hover table-striped reports-table"
                        style="width:100%">

                        <thead>

                            <tr class="primary">

                                <th style="text-align:left;">
                                    Purchase ID
                                </th>

                                <th style="text-align:left;">
                                    Reference No
                                </th>

                                <th style="text-align:left;">
                                    Date
                                </th>

                                <th style="text-align:left;">
                                    Supplier
                                </th>

                                <th style="text-align:left;">
                                    Product
                                </th>

                                <th style="text-align:right;">
                                    Quantity
                                </th>

                                <th style="text-align:right;">
                                    Unit Cost
                                </th>

                                <th style="text-align:right;">
                                    Subtotal
                                </th>

                                <th style="text-align:right;">
                                    Qty Balance
                                </th>

                                <th style="text-align:left;">
                                    Expiry
                                </th>

                                <th style="text-align:left;">
                                    Created By
                                </th>

                            </tr>

                        </thead>
                        <tfoot>
                            <tr class="active">
                                <th colspan="5">Total</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
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

        var table = $('#PurchaseData').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: '<?= admin_url("reports/purchaseslist_ajax"); ?>',
                type: 'POST',

                data: function(d) {

                    d.item_id = $('#item_id').val() || '';
                    d.item_name = $('#item_name').val() || '';
                    d.start_date = $('#start_date').val() || '';
                    d.end_date = $('#end_date').val() || '';

                    d['<?= $this->security->get_csrf_token_name(); ?>'] =
                        '<?= $this->security->get_csrf_hash(); ?>';
                }
            },

            pageLength: 25,

            lengthMenu: [
                [10, 25, 50, 100, 250],
                [10, 25, 50, 100, 250]
            ],

            searching: true,
            ordering: false,
            paging: true,
            info: true,


            // ==================================================
            // SHOW BUTTONS
            // ==================================================

            dom: '<"row no-print"' +
                '<"col-sm-4"l>' +
                '<"col-sm-8 text-right"Bf>' +
                '>' +
                'rt' +
                '<"row no-print"' +
                '<"col-sm-6"i>' +
                '<"col-sm-6 text-right"p>' +
                '>',


            // ==================================================
            // EXPORT BUTTONS
            // ==================================================

            buttons: [

                {
                    extend: 'copyHtml5',
                    text: '<i class="fa fa-copy"></i> Copy',
                    className: 'btn btn-default btn-sm'
                },

                {
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',

                    action: function(e, dt, node, config) {

                        var form = $('<form>', {
                            method: 'POST',
                            action: '<?= admin_url("reports/purchaseslist_export_excel"); ?>',
                            target: '_blank'
                        });

                        form.append(
                            $('<input>', {
                                type: 'hidden',
                                name: 'item_id',
                                value: $('#item_id').val() || ''
                            })
                        );

                        form.append(
                            $('<input>', {
                                type: 'hidden',
                                name: 'item_name',
                                value: $('#item_name').val() || ''
                            })
                        );

                        form.append(
                            $('<input>', {
                                type: 'hidden',
                                name: 'start_date',
                                value: $('#start_date').val() || ''
                            })
                        );

                        form.append(
                            $('<input>', {
                                type: 'hidden',
                                name: 'end_date',
                                value: $('#end_date').val() || ''
                            })
                        );

                        form.append(
                            $('<input>', {
                                type: 'hidden',
                                name: '<?= $this->security->get_csrf_token_name(); ?>',
                                value: '<?= $this->security->get_csrf_hash(); ?>'
                            })
                        );

                        $('body').append(form);

                        form.submit();

                        form.remove();
                    }
                },


                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-text-o"></i> CSV',
                    className: 'btn btn-info btn-sm'
                },

                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-sm',

                    orientation: 'landscape',
                    pageSize: 'A4'
                },

                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    className: 'btn btn-primary btn-sm'
                }

            ]

        });




        $('#purchaseFilterForm').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload(null, true);
        });
    });
</script>