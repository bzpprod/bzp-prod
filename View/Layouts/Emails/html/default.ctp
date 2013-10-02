<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $title_for_layout;?></title>
</head>
<body>
	<table width="550" border="0" vspace="0" cellspacing="0" cellpadding="0">
		<tr>
			<td><?php echo $this->Html->image('http://www.bazzapp.com/img/emails/default/header.jpg', array('width' => 550, 'height' => 63, 'alt' => 'BazzApp', 'url' => Configure::read('Facebook.redirect'))); ?></td>
		<tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td>
				<table width="490" align="center" border="0" vspace="0" cellspacing="0" cellpadding="0">
					<tr>
						<td><?php echo $content_for_layout;?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<tr>
			<td height="10" bgcolor="3B5999"></td>
		</tr>
	</table>
</body>
</html>