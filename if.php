<!DOCTYPE html>
<html>
	<head>
		<title>Neural Network</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<div id="title">Application of Artificial Neural Networks</div>
				<div id="subTitle"><sup>(with)</sup> Backpropagation Learning Method</div>
			</div>
			<div id="main">
				<div id="titleMain">Testing</div>
				<form action="backpropagationProcess.php?status=cal" method="post" id="formInfo">
					<table>
						<tr>
							<td><input type="text" name="uan" id="uan" placeholder="input uan value"></td>
							<td><input type="text" name="test" id="test" placeholder="input test value"></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: center"><button type="submit" name="submit">Calculate</button></td>
						</tr>
					</table>
				</form>
				<div id="dataTrain"><a href="backpropagationProcess.php?status=train">Data Train Result</a></div>
			</div>
			<div id="footer">
				<div>Damas Fajar Priyanto</div>
				<div>Mirra Prasasti</div>
				<div>Yuli Suprapto</div>
			</div>
		</div>
	</body>
</html>	