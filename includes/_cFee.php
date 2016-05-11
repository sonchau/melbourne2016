<?php 


	class FeeCalculator{


    	const FAMILY_DISCOUNT1_AMOUNT = 50;

	    const FAMILY_DISCOUNT2_AMOUNT = 100;

	    const AIRBED_DISCOUNT_AMOUNT = 20;

	    const AIRPORT_FEE = 25;

	    const EARLY_BIRD_DISCOUNT_AMOUNT = 40;


		function FeeCalculator(){


		}


		function calculateFee(	$Age = 0, 
								$FamilyDiscount = '-', 
								$Airbed = 0, 
								$AirportTransfer = 0){



		        $fee = 0;

		        //normal pricing
	            switch (true) {
	                case ($Age <= 5):
	                    $fee = 50;
	                    break;

	                case ($Age > 5 && $Age <= 12):
	                    $fee = 350;
	                    break;

	                case ($Age > 12 && $Age < 65):
	                    $fee = 440;
	                    break;
	                case ($Age >= 65):
	                    $fee = 390;
	                    break;
                    default:

						# code...

						break;

	            }



	            //any family discounts
				switch ($FamilyDiscount) {

					case '2nd child 5yo and under':
						//only apply if age is under 6
						if ($Age < 6){
							$fee = $fee - self::FAMILY_DISCOUNT1_AMOUNT;
						}
						break;

					case '2nd child over 5yo':
						$fee = $fee - self::FAMILY_DISCOUNT2_AMOUNT;
						break;

					default:
						# code...
						break;					

				}



				if ($Airbed) { //airbed discount
					$fee = $fee - self::AIRBED_DISCOUNT_AMOUNT;
				}


				//airport transfer fee
				if ($AirportTransfer) {
					$fee = $fee + self::AIRPORT_FEE;

				}


				//early bird special
				$fee = $this->calculateEarlyBirdDiscount($fee);



				//adjustment of fee
        		if ($fee < 0) { 
        			$fee = 0 ;
        		}


            	return $fee;

			}




			function calculateEarlyBirdDiscount($fee){
				if (is_numeric($fee)){

					if (new DateTime() < new DateTime("2016-09-30 00:00:00")) {
				    	$fee = $fee - self::EARLY_BIRD_DISCOUNT_AMOUNT;
				    	return $fee;
					}

				}else{
					return $fee;
				}
		
			}




	//end class
	}


?>