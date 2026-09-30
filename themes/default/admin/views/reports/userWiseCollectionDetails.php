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
                <div id="form" class="no-print">
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
                            <span id="reportDateRange"></span>
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
                    <table id=""
                        cellpadding="0"
                        cellspacing="0"
                        border="0"
                        class="table table-bordered table-condensed table-hover table-striped reports-table"
                        style="width:100%">

                        <thead>

                        </thead>
                        <tfoot>

                        </tfoot>

                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        function updateReportDateRange() {

            var start = $('#start_date').val();
            var end = $('#end_date').val();

            if (!start) {
                start = '<?= date('d/m/Y'); ?>';
            }

            if (!end) {
                end = '<?= date('d/m/Y'); ?>';
            }

            $('#reportDateRange').text(start + ' - ' + end);
        }

        updateReportDateRange();

        $('#saleFilterForm').on('submit', function(e) {

            e.preventDefault();

            updateReportDateRange();

            table.ajax.reload(null, true);
        });


        var table = $('#').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: '<?= admin_url("reports/"); ?>',
                type: 'POST',

                data: function(d) {

                    d.start_date =
                        $('#start_date').val() || '';

                    d.end_date =
                        $('#end_date').val() || '';

                    d.user =
                        $('#select_user').val() || '';

                    d['<?= $this->security->get_csrf_token_name(); ?>'] =
                        '<?= $this->security->get_csrf_hash(); ?>';
                },

                dataSrc: function(json) {
                    var grandTotal = parseFloat(json.grand_total || 0);

                    $('#grandTotal').text(
                        '৳ ' + grandTotal.toLocaleString('en-IN', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        })
                    );

                    return json.data;
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

            dom: '<"row no-print"' +
                '<"col-sm-4"l>' +


                buttons: [

                    // ----------------------------------------
                    // COPY
                    // ----------------------------------------

                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-copy"></i> Copy',
                        className: 'btn btn-default btn-sm'
                    },

                    // ----------------------------------------
                    // EXCEL / CSV EXPORT FROM SERVER
                    // ----------------------------------------

                    {
                        text: '<i class="fa fa-file-excel-o"></i> Excel',

                        className: 'btn btn-success btn-sm',

                        action: function(e, dt, node, config) {

                            var form = $('<form>', {
                                method: 'POST',
                                action: '<?= admin_url("reports/export_excel"); ?>',
                                target: '_blank'
                            });

                            // User
                            form.append(
                                $('<input>', {
                                    type: 'hidden',
                                    name: 'user',
                                    value: $('#select_user').val() || ''
                                })
                            );

                            // Start Date
                            form.append(
                                $('<input>', {
                                    type: 'hidden',
                                    name: 'start_date',
                                    value: $('#start_date').val() || ''
                                })
                            );

                            // End Date
                            form.append(
                                $('<input>', {
                                    type: 'hidden',
                                    name: 'end_date',
                                    value: $('#end_date').val() || ''
                                })
                            );

                            // CSRF
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

                    // ----------------------------------------
                    // CSV
                    // ----------------------------------------

                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text-o"></i> CSV',
                        className: 'btn btn-info btn-sm'
                    },

                    // ----------------------------------------
                    // PDF
                    // ----------------------------------------

                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i> PDF',
                        className: 'btn btn-danger btn-sm',

                        orientation: 'landscape',
                        pageSize: 'A4'
                    },

                    // ----------------------------------------
                    // PRINT
                    // ----------------------------------------

                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        className: 'btn btn-primary btn-sm'
                    }
                ]
        });



        $('#saleFilterForm').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload(null, true);
        });
    });
</script>