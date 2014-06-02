<?php

	$numEpoh = 50;
	$LR = 0.6;
	$numHL = 2;

	$ruan = array( 8.75, 9.13, 9.00, 9.25, 8.25, 8.13, 9.00, 8.88, 8.50, 8.25  );
	$test = array( 6.33, 5.67, 7.50, 7.33, 5.17, 4.17, 6.33, 7.00,  7.33, 4.50 );
	$target = array( "IPA", "IPS", "IPA", "IPA", "IPS", "IPS", "IPA", "IPA", "IPA", "IPS");

	$HLIn = array();
	$HLOut = array();
	$initHL = 1;

	$mseOut = array();
	$dy1 = array();
	$dy2 = array();
	$dz = array();
	$dbias2 = array();

	$mseIn = array();
	$mse = array();

	$minR = min($ruan);
	$maxR = max($ruan);
	$minT = min($test);
	$maxT = max($test);

	$ruanNorm = array(); //array normalisasi rata2 uan
	$testNorm = array();
	$targetNorm = array();

	if (isset($_POST['submit'])) {
		
		$uanData = $_POST['uan'];
		$testData = $_POST['test'];

		mind();

		normalisasiData();

		test();

	}

	function mind(){

		global $numEpoh;
		global $mseOut;
		global $HLOut;

		normalisasi();

		for ($x = 0; $x < $numEpoh; $x++) { 
			
			$x2 = $x+1;

			// echo "<strong>epoh " . $x2."</strong> ";

			perhitungan();
			
			// echo "<strong>mseOut</strong> ".$mseOut[0]." ";
			// echo "<strong>y</strong> ".$HLOut[0][4]." ";
			// echo "<strong>mseOut</strong> ".$mseOut[1]." ";
			// echo "<strong>y</strong> ".$HLOut[1][4]."</br>";
		}

	}

	////////////	/////////
	// fungsi normalisasi 
	///////////		/////////

	function normalisasi(){

		global $ruan;
		global $test;
		global $target;
		global $minR;
		global $maxR;
		global $minT;
		global $maxT;
		global $ruanNorm;
		global $testNorm;
		global $targetNorm;
		
		for ($x=0; $x < count($ruan); $x++) { 
			
			$ruanNorm[$x] = ($ruan[$x]-$minR)/($maxR-$minR); //rumus normalisasi rata2 uan
			$testNorm[$x] = ($test[$x]-$minT)/($maxT-$minT);

			echo $ruanNorm[$x]."</br>";
			echo $testNorm[$x]."</br>";
			
			if ($target[$x] == "IPA") {
				$targetNorm[$x][0] = 1;
				$targetNorm[$x][1] = 0;
			} else if( $target[$x] == "IPS"){
				$targetNorm[$x][0] = 0;
				$targetNorm[$x][1] = 1;
			}

			////////////////////////
			// tampilkan normalisasi
			////////////////////////
			
			for ($i=0; $i < 2; $i++) {   
				echo $targetNorm[$x][$i];
				if ($i == 1) {
					echo "</br></br>";
				}				
			}

		}

	}

	///////////		/////////////
	// fungsi pada perhitungan
	///////////		/////////////

	function perhitungan(){

		global $LR;
		global $HLIn;
		global $HLOut;
		global $numHL;
		global $initHL;

		global $ruanNorm;
		global $testNorm;
		global $targetNorm;

		global $mseOut;
		global $mseIn;
		global $mse;
		global $dy1;
		global $dy2;
		global $dz;
		global $dbias2;
		// $person = 0;

		//////////////////////////
		// $HLIn[$x][0] = uan
		// $HLIn[$x][1] = test
		// $HLIn[$x][2] = bias
		// $HLIn[$x][3] = z_in
		// $HLIn[$x][4] = z
		//////////////////////////

		//////////////////////////
		// $HLOut[$x][0] = z1
		// $HLOut[$x][1] = z2
		// $HLOut[$x][2] = bias2
		// $HLOut[$x][3] = y_in
		// $HLOut[$x][4] = y
		//////////////////////////

		for ($person=0; $person < count($testNorm); $person++) { 
			
			// $no = $person + 1;
			// echo "</br>person".$no."</br>";

			if ($initHL == 1) {
				for ($x = 0; $x < $numHL; $x++){

					for ($y = 0; $y < 3 ; $y++) { 
						
						$HLIn[$x][$y] = rand(1, 10)/10;
						$HLOut[$x][$y] = rand(1, 10)/10; //sekalian y

						// echo "hlin $x ".$HLIn[$x][$y];
						// echo "hlout $x".$HLOut[$x][$y]."</br>";

					}

					$HLIn[$x][3] = $HLIn[$x][2]+($HLIn[$x][0]*$ruanNorm[$person]+$HLIn[$x][1]*$testNorm[$person]); // z_in

					$HLIn[$x][4] = 1/(1+exp(-$HLIn[$x][3]));

					// echo $HLIn[$x][3]."</br>";
					// echo $HLIn[$x][4]."</br>";

				}

				for ($x = 0; $x < $numHL; $x++){

					$HLOut[$x][3] = $HLOut[$x][2]+($HLOut[$x][0]*$HLIn[0][4])+($HLOut[$x][1]*$HLIn[1][4]); // y_in
					
					$HLOut[$x][4] = 1/(1+exp(-$HLOut[$x][3])); // y

					// echo "hl out yin ".$HLOut[$x][3]."</br>";
					// echo "hl out y ".$HLOut[$x][4]."</br>"; 

					$mseOut[$x] = ($targetNorm[$person][$x]-$HLOut[$x][4])*((1/(1+exp(-$HLOut[$x][3])))*(1-1/(1+exp(-$HLOut[$x][3]))));

					// echo "mseOut ".$mseOut[$x]."</br>";

					$dy1[$x] = $LR * $HLOut[$x][0] * $mseOut[$x];

					$dy2[$x] = $LR * $HLOut[$x][0] * $dy1[$x];

					$dbias2[$x] = $LR * $mseOut[$x];


				}
				
				for ($x=0; $x < 2 ; $x++) { 

					$mseIn[$x] = ($mseOut[0]*$HLOut[0][$x])+($mseOut[1]*$HLOut[1][$x]);
					$mse[$x] = $mseIn[$x] * ((1/(1+exp(-$HLIn[$x][3])))*(1-1/1+exp(-$HLIn[$x][3])));

					for ($y = 0; $y < 3; $y++) { 
						
						$dz[$x][$y] = $mse[$x] * $HLIn[$x][$y];
						
					}

				}


				$initHL = 0;

			//////////////////////////
			// kedua dan seterusnya
			//////////////////////////

			} else {

				for ($x = 0; $x < $numHL; $x++){

					for ($y = 0; $y < 3 ; $y++) { 
						
						$HLIn[$x][$y] = $HLIn[$x][$y] + $dz[$x][$y];

					}

					$HLOut[$x][0] = $HLOut[$x][0] + $dy1[$x];
					$HLOut[$x][1] = $HLOut[$x][1] + $dy2[$x];
					$HLOut[$x][2] = $HLOut[$x][2] + $dbias2[$x];

					$HLIn[$x][3] = $HLIn[$x][2]+($HLIn[$x][0]*$ruanNorm[$person]+$HLIn[$x][1]*$testNorm[$person]); // z_in

					$HLIn[$x][4] = 1/(1+exp(-$HLIn[$x][3]));


					// echo $HLIn[$x][3];
					// echo $HLIn[$x][4];


				}

				for ($x = 0; $x < $numHL; $x++){

					$HLOut[$x][3] = $HLOut[$x][2]+($HLOut[$x][0]*$HLIn[0][4])+($HLOut[$x][1]*$HLIn[1][4]); // y_in
					
					$HLOut[$x][4] = 1/(1+exp(-$HLOut[$x][3])); // y

					// echo "hl out yin ".$HLOut[$x][3]."</br>";
					// echo "hl out y ".$HLOut[$x][4]."</br>"; 

					$mseOut[$x] = ($targetNorm[$person][$x]-$HLOut[$x][4])*((1/(1+exp(-$HLOut[$x][3])))*(1-1/(1+exp(-$HLOut[$x][3]))));

					// echo "mseOut".$mseOut[$x]."</BR>";

					$dy1[$x] = $LR * $HLOut[$x][0] * $mseOut[$x];

					$dy2[$x] = $LR * $HLOut[$x][0] * $dy1[$x];

					$dbias2[$x]= $LR * $mseOut[$x];


				}
				
				for ($x=0; $x < 2 ; $x++) { 

					$mseIn[$x] = ($mseOut[0]*$HLOut[0][$x])+($mseOut[1]*$HLOut[1][$x]);
					$mse[$x] = $mseIn[$x] * ((1/(1+exp(-$HLIn[$x][3])))*(1-1/1+exp(-$HLIn[$x][3])));

					// echo $mseIn[$x];
					// echo $mse[$x]."</br>";

					for ($y = 0; $y < 3; $y++) { 
						
						$dz[$x][$y] = $mse[$x] * $HLIn[$x][$y];

						// echo "delta z ".$dz[$x][$y]."</br>";
						
					}

				}


			}
			


		}
	}

	///////////		/////////////
	// fungsi normalisasi data
	///////////		/////////////

	function normalisasiData(){

		global $uanData;
		global $testData;
		global $minR;
		global $maxR;
		global $minT;
		global $maxT;

		if ($uanData < $minR) $minR = $uanData;
		if ($uanData > $maxR) $maxR = $uanData;
		if ($testData < $minT) $minT = $testData;
		if ($testData > $maxT) $maxT = $testData;
		

		$newDataUan = ($uanData-$minR)/($maxR-$minR);
		$newDataTest = ($testData-$minT)/($maxT-$minT);

	}

	///////		///
	// fungsi test
	///////		///

	function test(){

		global $LR;
		global $HLIn;
		global $HLOut;
		global $numHL;

		global $mseOut;
		global $mseIn;
		global $mse;
		global $dy1;
		global $dy2;
		global $dz;

		global $newDataUan;
		global $newDataTest;

		global $dbias2;

		for ($x = 0; $x < 2; $x++){

			for ($y = 0; $y < 3 ; $y++) { 
				
				$HLIn[$x][$y] = $HLIn[$x][$y] + $dz[$x][$y];

			}

			$HLOut[$x][0] = $HLOut[$x][0] + $dy1[$x];
			$HLOut[$x][1] = $HLOut[$x][1] + $dy2[$x];
			$HLOut[$x][2] = $HLOut[$x][2] + $dbias2[$x];

			$HLIn[$x][3] = $HLIn[$x][2]+($HLIn[$x][0]*$newDataUan+$HLIn[$x][1]*$newDataTest); // z_in

			$HLIn[$x][4] = 1/(1+exp(-$HLIn[$x][3]));

		}

		for ($x = 0; $x < 2; $x++){

			$HLOut[$x][3] = $HLOut[$x][2]+($HLOut[$x][0]*$HLIn[0][4])+($HLOut[$x][1]*$HLIn[1][4]); // y_in
			
			$HLOut[$x][4] = 1/(1+exp(-$HLOut[$x][3])); // y

			echo "real target ".$HLOut[$x][4]."</br>";

			// $targetTest[$x] = ($mseOut[$x]/($HLOut[$x][4]*(1-$HLOut[$x][4])))+$HLOut[$x][4];

			// echo $mseOut[$x]."</br>";

			// echo "target ".$targetTest[$x]."</br>";

			// $mseOut[$x] = ($targetNorm[$person][$x]-$HLOut[$x][4])*((1/(1+exp(-$HLOut[$x][3])))*(1-1/(1+exp(-$HLOut[$x][3]))));

			// $dy1[$x] = $LR * $HLOut[$x][0] * $mseOut[$x];

			// $dy2[$x] = $LR * $HLOut[$x][0] * $dy1[$x];

			// $dbias2[$x][$y] = $LR * $mseOut[$x];


		}
		
		// for ($x=0; $x < 2 ; $x++) { 

		// 	$mseIn[$x] = ($mseOut[0]*$HLOut[0][$x])+($mseOut[1]*$HLOut[1][$x]);
		// 	$mse[$x] = $mseIn[$x] * ((1/(1+exp(-$HLIn[$x][3])))*(1-1/1+exp(-$HLIn[$x][3])));

		// 	for ($y = 0; $y < 3; $y++) { 
				
		// 		$dz[$x][$y] = $mse[$x] * $HLIn[$x][$y];
				
		// 	}

		// }

	}

?>