<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/_cRegistration.php');?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        View | Vietnamese Christians Fellowship Conference in Australia – Melbourne 2016
    </title>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/_scripts.php');?>

	<style type="text/css">

		.panel-footer button {
			display: none;
		}

		#process-rego-button {
			display: block;
			min-width: 50%;
			margin: 0 auto;

		}

        td.cancelled { text-decoration: line-through; color: maroon; }
        .view-only { display: block !important; }
	</style>

</head>

<body>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/_menu.php');?>


    <div class="container body-content">

            <h1>Registration Information</h1>

            <p>Below is your registration information that we have on record. Please ensure that your details are correct and recent in case we need to contact you for conference updates.</p>

            <p>If you need to update your information, please contact our team at: <a href="mailto:info@melbourne2016.net.au">info@melbourne2016.net.au</a></p>

            <div>&nbsp;</div>

            <?php 

            	if ($_GET["ref"] && $_GET["success"]) {

						echo  '<div class="alert alert-dismissible alert-success font-normal no-print"><button type="button" class="close" data-dismiss="alert">X</button><i class="fa fa-smile-o fa-5x pull-left"> </i><p><strong>Awesome!</strong> You have successfully submitted your registration details for Dai Hoi Melbourne 2016, please take note of your green reference number below. <p>On behalf of the conference, thank you and God bless.</p><div class="clearfix"></div></div>';

								

            	}

            ?>



            <div>&nbsp;</div>

            <div class="row" id="rego-summary" style="display: block !important;">

                <div class="well">

                    <div id="summary-content">

                        <?php viewRego(); ?> 

                    </div>

                    <div class="clearfix">&nbsp;</div>

                </div>

            </div>




    </div>

    <!-- footer -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/_footer.php');?> 
    

</body>

</html>



