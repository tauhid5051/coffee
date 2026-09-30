<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('User Wise Collection'); ?></h2>

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

                    <?php echo admin_form_open('reports/UserWiseCollection1', 'autocomplete="off"'); ?>

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('user', 'user') ?>
                                <?php
                                $cus[''] = lang('select') . ' ' . lang('user');
                                foreach ($allStaff as $customer) {

                                    $cus[$customer->id] = trim(
                                        $customer->first_name . ' ' . $customer->last_name
                                    );
                                }
                                echo form_dropdown('customer', $cus, ($_POST['customer'] ?? ''), 'class="form-control select" id="select_customer" placeholder="' . lang('select') . ' ' . lang('user') . '" style="width:100%"')
                                ?>
                            </div>
                        </div>




                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('start_date', 'start_date'); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : date("d/m/Y")), 'class="form-control date"   id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang('end_date', 'end_date'); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : date("d/m/Y")), 'class="form-control date"  id="end_date"'); ?>
                            </div>
                        </div>

                    </div>
                    <br>
                    <div class="form-group">
                        <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line('submit'), 'class="btn btn-primary"'); ?> </div>
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
                        <p> User Wise Collection </p>
                        <!-- <br> -->
                        <p>
                            Date Range: <?= (isset($_POST['start_date']) ? $_POST['start_date'] : date('d/m/Y'))  ?>
                            <span id="sep"></span>
                            <?= (isset($_POST['end_date']) ? $_POST['end_date'] : date('d/m/Y')) ?>
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



                <div>
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0" class="table table-bordered table-condensed table-hover table-striped reports-table">
                        <thead>
                            <tr class="primary">
                                <th style=" text-align: left; "><?= lang('User'); ?></th>
                                <th style=" text-align: end; "><?= lang('Invoice'); ?></th>
                                <th style=" text-align: end; "><?= lang('Total Bill'); ?></th>
                                <th style=" text-align: end; "><?= lang('Cash'); ?></th>
                                <th style=" text-align: end; "><?= lang('Other'); ?></th>
                                <th style=" text-align: end; "><?= lang('Total Collection'); ?></th>
                                <th style=" text-align: end; "><?= lang('Due'); ?></th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as  $item) : ?>
                                <tr>
                                    <th style=" text-align: left; "><?php echo ($item['first_name'] . ' ' . $item['last_name']); ?></th>
                                    <td style=" text-align: end; "><?php echo intval($item['total_invoice']); ?></td>
                                    <td style=" text-align: end; "><?php echo intval($item['total_amount']); ?></td>
                                    <td style=" text-align: end; "><?php echo intval($item['cash']);  ?></td>
                                    <td style=" text-align: end; "><?php echo ($item['paid'] - $item['cash']); ?></td>
                                    <td style=" text-align: end; "><?php echo intval($item['paid']); ?></td>
                                    <td style=" text-align: end; "><?php echo intval($item['balance']); ?></td>


                                </tr>
                            <?php endforeach; ?>

                            <tr>
                                <th>Total</th>
                                <th style=" text-align: end; "><?php echo $totalInvoice; ?></th>
                                <th style=" text-align: end; "><?php echo $totalAmount; ?></th>
                                <th style=" text-align: end; "><?php echo $totalCash; ?></th>
                                <th style=" text-align: end; "><?php echo ($totalPaid - $totalCash); ?></th>
                                <th style=" text-align: end; "><?php echo $totalPaid; ?></th>
                                <th style=" text-align: end; "><?php echo $totalBalance; ?></th>

                            </tr>

                        </tbody>
                        <tfoot class="no-print">
                            <!-- <tr class="active">
                            <th style=" text-align: left; "><?= lang('User'); ?></th>
                                <th style=" text-align: end; "><?= lang('Invoice'); ?></th>
                                <th style=" text-align: end; "><?= lang('Total Bill'); ?></th>
                                <th style=" text-align: end; "><?= lang('Cash'); ?></th>
                                <th style=" text-align: end; "><?= lang('Other'); ?></th>
                                <th style=" text-align: end; "><?= lang('Total Collection'); ?></th>
                                <th style=" text-align: end; "><?= lang('Due'); ?></th>

                            </tr> -->
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        var selectedCustomer = $('#user').find(":selected").text();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();

        $('#branch_name').empty();
        if (selectedCustomer != "All") {
            $('#branch_name').empty().append(selectedCustomer);
        }

        if (start_date && end_date) {
            $("#sep").empty().append(" - ");
        }

        $('#pdf').click(function(event) {
            event.preventDefault();
            window.location.href = "<?= admin_url('reports/getCustomers/pdf') ?>";
            return false;
        });
        $('#xls').click(function(event) {
            event.preventDefault();
            window.location.href = "<?= admin_url('reports/getCustomers/0/xls') ?>";
            return false;
        });
        $('#image').click(function(event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function(canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>