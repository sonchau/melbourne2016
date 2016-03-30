<div>

    <div class="print-only hidden col-lg-12">
        <!-- <h4 style="color: #CAAB71;     border-bottom: 1px solid #DF691A ;     padding-bottom: 10px;     margin-bottom: 25px;">Viewing Your Registraion</h4> -->
                    <table class="table table-responsive table-bordered table-striped" style="margin-bottom:10px">
                        <caption>Viewing Your Registraion</caption>
                    </table>        
            <div>
                
                <img style="float: left; margin: 0 10px 10px 10px;" src="https://chart.googleapis.com/chart?chs=120x120&cht=qr&chl=melbourne2016.net.au/register/view?ref={REFERENCE}&choe=UTF-8&chld=Q|0" 
                align="left" />
            

                <p>
                    You can view your registration at anytime using the following link <a href="http://melbourne2016.net.au/register/view/?ref={REFERENCE}">http://melbourne2016.net.au/</a>, along with your reference: <b>{REFERENCE}</b>
                    <p> For those more technology adept, you can use the QR Code on the left.</p>
                </p>
                
                
                <div class="clearfix">&nbsp;</div>            
        </div>
        <div>&nbsp;</div>    
    </div>
    <div>&nbsp;</div>
    <div class="col-lg-6">

        <table class="table table-responsive table-bordered">
            <caption>Main Contact</caption>
            <thead>
                <tr>
                    <th colspan="2">{0}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{1}</td>
                    <td>{2}yo</td>
                </tr>
                <tr>
                    <td>{3}</td>
                    <td>{4}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <!-- <span style="display: none">Airbed Discount: {5}</span> -->
                        Airport Transfer: {6}
                    </td>
                    
                </tr>
                <tr>
                    <td colspan="2">{7}</td>
                </tr>
                <tr>
                    <td>Fee</td>
                    <td>${8}</td>
                </tr>
            </tbody>
        </table>

    </div>

    <div class="col-lg-6">

        <table class="table table-responsive table-bordered ">
            <caption>Additional Notes</caption>
            <thead>
                <tr>
                    <th>&nbsp; </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{9}</td>
                </tr>
            </tbody>
        </table>

    </div>

    <div class="col-lg-12 {10}">
        <div class="table-responsive">
            <table class="table table-responsive table-bordered table-striped table-last-col-right">
                <caption>Additional Registrants</caption>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Relation</th>
                        <th>Family<br>Discount</th>
                        <!--<th>Airbed<br>Discount</th>-->
                        <th>Airport<br>Transfer</th>
                        <th>Fee</th>
                    </tr>
                </thead>
                <tbody>
                    {11}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"></td>
                        <td style="border-bottom: 3px double black; border-top: 2px solid black">${12}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="clearfix">&nbsp;</div>
    <div class="clearfix">&nbsp;</div>
    <div class="clearfix">&nbsp;</div>

    <div class="col-lg-12" id="payment-summary">

        <div class="panel panel-default">
            
            <div class="panel-body">

                <div class="row">


                    <div class="col-lg-6">


                                <table class="table table-responsive table-bordered table-striped table-last-col-right">
                                    <caption>Payment Calculation</caption>
                                    <thead>
                                        <tr>
                                            <th> </th>
                                            <th>Name</th>
                                            <th>Age</th>
                                            <th>Fee</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {13}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td>
                                                TOTAL PAYABLE: <span class="label label-success label-summary-total">${14}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>



                    </div>


                    <div class="col-lg-6 text-left">

                        <table class="table table-responsive table-bordered table-striped" style="margin-bottom:10px">
                            <caption>Bank Details</caption>
                            <thead>
                                <tr>
                                    <th>Please make all payments to the following Bank Details.</th>
                                </tr>
                            </thead>
                        </table>
                            <div class="alert alert-info bank-info">
                                <p>Please include your Registered Name and Your Reference in the payment.</p>
                                <?php include($_SERVER["DOCUMENT_ROOT"] . "/includes/_bankdetails.php");?>
                             
                            </div>


                    </div>


                </div>

                <div class="print-only hidden row view-only">
                    <div class="col-md-6 col-lg-6">

                        <table class="table table-responsive table-bordered table-striped" style="margin-bottom:10px">
                            <caption>Airport</caption>
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                        </table>
                        <div>
                            <p>
                                <span class="label label-warning">IMPORTANT</span>
                                Last Bus leaves Tullamarine Airport at 1pm on 27th Dec 2016. No pick up can be made from airport after this time.
                            </p>
                            <p>
                                Airport transfers from and to airport on the 27th and the 31st can be arranged if required – booking
                                before December required and separate fees applied.
                            </p>
                        </div>
                        <div>&nbsp;</div>
                    </div>                    
                    <div class="col-md-6 col-lg-6">

                        <table class="table table-responsive table-bordered table-striped" style="margin-bottom:10px">
                            <caption>Accomodation</caption>
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                        </table>
                        <span class="label label-warning">IMPORTANT</span>
                        <p>We unfortunately have only 350 rooms available, with each bedroom having one single bed and one air bed in the room. Air beds will be eligilble for a $20 discount.</p>
                        <div>&nbsp;</div>
                    </div>


                </div>


                <div class="clearfix">&nbsp;</div>
            </div>
            <div class="panel-footer text-center">
                <button type="button" class="btn btn-primary no-print"
                        onclick="SUBMISSION.submitRegistration(this)" id="process-rego-button" 
                        data-loading-text="&lt;i class=&#39;fa fa-circle-o-notch fa-spin&#39;&gt;&lt;/i&gt; Processing, please wait...">
                    REGISTER ME !
                </button>

                <button type="button" class="btn btn-default no-print" onclick="swapRegoSummary();">CANCEL</button>
                
            </div>
        </div>

    </div>

</div>