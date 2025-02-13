<?php

session_start();

if (isset($_GET['reset'])) unset($_SESSION['fonts']);

if (isset($_POST["get-font"])) {

	//if (!isset($_POST['size']) || gettype($_POST['size']) != "integer" || $_POST['size'] < 3) exit();
	$size = $_POST['size'];

	if (!isset($_POST['font'])) exit();
	$font = escapeshellarg("fonts/" . $_POST['font']);
	$s = ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
	if (isset($_POST['includedGlyphs'])) {
		$s = $_POST['includedGlyphs'];
	}
	$s = escapeshellarg($s);

	exec("./fontconvert $font $size -s $s", $output, $retval);
	if ($retval != 0) exit();

	$filename = $output[count($output) - 6];
	$filename = str_replace("const GFXfont ", "", $filename);
	$filename = str_replace(" PROGMEM = {", ".h", $filename);


	header("Content-Disposition: attachment; filename=\"$filename\"");

	foreach ($output as $line) echo "$line\n";

	exit();
}


if (!isset($_SESSION['fonts'])) $_SESSION['fonts'] = array();

// Delete fonts from session variable if the disk file is not there anymore
foreach ($_SESSION['fonts'] as $index => $font) {
	if (!file_exists("fonts/user/$font")) unset($_SESSION['fonts'][$index]);
}


$select_font = "";
if (isset($_POST["submit-file"])) {
	$target_dir = "fonts/user/";
	$filename = basename($_FILES["fileToUpload"]["name"]);
	$target_file = $target_dir . $filename;
	$select_font = "user/$filename";

	if ((strtolower(substr($target_file, -4)) == ".ttf") or (strtolower(substr($target_file, -4)) == ".otf")) {
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			if (!in_array($filename, $_SESSION['fonts'])) {
				array_push($_SESSION['fonts'], $filename);
				if (count($_SESSION['fonts']) > 5) array_shift($_SESSION['fonts']);
			}
		}
	}
}

?>

<html>

<head>
	<title>truetype2gfx - Converting fonts from TrueType to Adafruit GFX</title>

	<style>
		body {
			background-color: #000000;
			color: #ffffff;
			margin: 100px;
			margin-top: 100px;
			margin-left: 100px;
			font-family: Verdana, sans-serif;
		}
		a {
			text-decoration: none;
			font-weight: bold;
			color: #8080FF;
		}
		td {
			vertical-align: top;
		}
		table {
			width: 960px;
		}
		td#first {
			margin: 0px;
			padding-top: 90px;
			padding-left:50px;
			width: 480px;
			height: 429px;
		}
		td#second {
			width: 240px;
		}
		td#third {
			width: 240px;
		}
		#textfield {
			width: 200px;
		}
		#widthfield {
			width: 70px;
		}
		#heightfield {
			width: 70px;
		}
		#sizefield {
			width: 35px;
			text-align: center;
		}
		#get-font {
			width: 200px;
			height: 30px;
			font-size: 20px;
			font-weight: bold;
		}
	</style>
</head>

<body onload = 'setFont()'>


	&nbsp;<br>

	<table>
		<tr>
			<td colspan=3>
				<h2>truetype2gfx - Converting fonts from TrueType to Adafruit GFX</h2>
				&nbsp;<br>
				&nbsp;<br>
			</td>
		</tr>

		<tr>
			<td id="first">
				<img id="image" src="image.php">
			</td>
			<td id="second">

				<form action="" method="post" enctype="multipart/form-data">

				<h3>FreeFonts</h3>
				<input type="radio" name="font" value="FreeSans.ttf" checked onChange="updateImage()"> FreeSans<br>
				<input type="radio" name="font" value="FreeSansBold.ttf" onChange="updateImage()"> FreeSansBold<br>
				<input type="radio" name="font" value="FreeSansBoldOblique.ttf" onChange="updateImage()"> FreeSansBoldOblique<br>
				<input type="radio" name="font" value="FreeSansOblique.ttf" onChange="updateImage()"> FreeSansOblique<br>
				<input type="radio" name="font" value="FreeSerif.ttf" onChange="updateImage()"> FreeSerif<br>
				<input type="radio" name="font" value="FreeSerifBold.ttf" onChange="updateImage()"> FreeSerifBold<br>
				<input type="radio" name="font" value="FreeSerifBoldItalic.ttf" onChange="updateImage()"> FreeSerifBoldItalic<br>
				<input type="radio" name="font" value="FreeSerifItalic.ttf" onChange="updateImage()"> FreeSerifItalic<br>
				<input type="radio" name="font" value="FreeMono.ttf" onChange="updateImage()"> FreeMono<br>
				<input type="radio" name="font" value="FreeMonoBold.ttf" onChange="updateImage()"> FreeMonoBold<br>
				<input type="radio" name="font" value="FreeMonoBoldOblique.ttf" onChange="updateImage()"> FreeMonoBoldOblique<br>
				<input type="radio" name="font" value="FreeMonoOblique.ttf" onChange="updateImage()"> FreeMonoOblique<br>

				<h3>Your fonts</h3>
				<?php
					foreach ($_SESSION['fonts'] as $font) {
						echo "<input type=\"radio\" name=\"font\" value=\"user/$font\" onChange=\"updateImage()\"> " . str_replace(".TTF", "", str_replace(".ttf", "", $font)) . "<br>\n";
					}
				?>

				&nbsp;<br>

				<input type="submit" value="Upload" name="submit-file" onClick="return validateUpload();"> <input type="file" name="fileToUpload" id="fileToUpload">
			</td>
			<td id="third">
				<h3>Font Size</h3>
				<input type="text" name="size" id="sizefield" value="16" onInput="updateImage()"> points

				&nbsp;<br>
				&nbsp;<br>

				<h3>Screen Size</h3>
				<input type="text" name="width" id="widthfield" value="200" onInput="updateImage()"> x
				<input type="text" name="height" id="heightfield" value="200" onInput="updateImage()">

				&nbsp;<br>
				&nbsp;<br>

				<h3>Demo text</h3>
				<input type="text" name="text" id="textfield" value="Testing 123..." onInput="updateImage()">

				&nbsp;<br>
				&nbsp;<br>

				<h3>Glyphs to include</h3>
				<textarea rows="8" cols="32" name="includedGlyphs" id="includedGlyphs"> !&quot;#$%&amp;'()*+,-./0123456789:;&lt;=&gt;?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~</textarea>

				&nbsp;<br>
				&nbsp;<br>

				<input type="submit" id="get-font" value="Get GFX font file" name="get-font">

				</form>

			</td>
		</tr>

		<tr>
			<td colspan=3>

&nbsp<br>

<h3>Introducing truetype2gfx</h3>

<p>Many Arduino projects and ready-built devices come with a
display. And the Adafruit GFX display driver is used by many of them
to display variable-width fonts. Some fonts usually are included with
the driver, and then there's a complicated procedure for adding your
own fonts. It involves compiling tools and a trial-and-error process
for figuring out how big the font will turn out on your display.</p>

<p>But now you can skip all that and easily convert the fonts your
Arduino project needs. No need to compile tools, no more guessing how
big a font will be. Just select a FreeFont or upload any TrueType or
OpenType font, select a size, specify which glyphs you want, download
the include file and you're ready to use the font in your project.</p>

<h3>The size thing</h3>

<p>Font sizes are given in points, where a point is 1/72 of an inch,
describing the actual size on a display. Or that's what it's supposed
to mean, but pretty much everyone that uses the Adafruit software
keeps the setting of 141 pixels per inch. In the Adafruit software it
says:</p>

<blockquote><code>#define DPI 141 // Approximate res. of Adafruit 2.8" TFT </code></blockquote>

<p>But since everyone keeps the setting, a certain font at 20 points
is going to take up the same number of <i>pixels</i> on a lot of
devices. <a href="https://iamvdo.me/en/blog/css-font-metrics-line-height-and-vertical-align">And
different fonts displaying at radically different sizes due to metrics
included in the font.</a> But I don't have to care about that: when I
make gfx fonts and include them on my device, they are the same size
as they are on the virtual device on the screen above. (Adjust the
screen dimensions to match the device you're using.)</p>

<h3>Your own fonts</h3>

<p>TrueType and OpenType fonts are everywhere online. At the time of
writing this, you can get loads and loads of pretty TrueType fonts
at <a href="https://www.1001freefonts.com">1001freefonts</a>,
<a href="https://www.dafont.com">dafont</a>,
or <a href="https://fonts.google.com">Google Fonts</a>. But
the best in my opinion is
<a href="https://www.myfonts.com">MyFonts</a>. MyFonts has loads of
free and commercial fonts and great tools for finding them.</p>

<p>Using this tool, you can upload your favorite font then view and
convert up to five fonts (which are only available to you). If you
upload a sixth font, the first one disappears. Also note that these
fonts will only last as long as your PHP session does, so if you come
back a day later, your fonts may be gone. It's really only meant to be
for immediate use.</p>

<p>If after downloading the font you discover it isn't exactly what
you wanted, there's a
nice <a href="https://tchapi.github.io/Adafruit-GFX-Font-Customiser/">online
GFX font editing tool</a> that lets you pixel edit the glyphs as well
as add or remove glyphs from the font.</p>

<h3>Example</h3>

<p>I'm a big fan of the <b>OpenSans</b> family, available
from <a href="https://fonts.google.com/specimen/Open+Sans">Google
Fonts</a> (and MyFonts).  I downloaded the family and then uploaded
"OpenSans-Regular.ttf". I fiddled with the size until it looked nice
at 12 points. I only need lowercase letters so I made sure "Glyphs to
include" was just "abcdefghijklmnopqrstuvwxyz" and I then hit the "Get
GFX font file" button and my browser downloaded a file called
"OpenSans_Regular12pt7b.h".  I then added the
"OpenSans_Regular12pt7b.h" from my "Download" directory as a second
tab with "Sketch / Add file..." in the Arduino IDE, and included it
with <code>#include "OpenSans_Regular12pt7b.h"</code></p>

<h3>Source code, bug reports, questions, etc..</h3>

<p>This tool has
a <a href="https://github.com/charles-haynes/truetype2gfx">github
repository</a> that has the (quick-hack-style) PHP/Javascript code
behind all this. If you have any questions, bug reports or
suggestions,
please <a href="https://github.com/charles-haynes/truetype2gfx/issues/new">open
an issue</a> and I'll see what I can do. </p>

			</td>
		</tr>
	</table>



	<script>

		function updateImage() {
			document.getElementById("image").src = "image.php?font=" + font() + "&size=" + document.getElementById("sizefield").value + "&width=" + document.getElementById("widthfield").value + "&height=" + document.getElementById("heightfield").value + "&text=" + document.getElementById("textfield").value + "#" + new Date().getTime();
		}

		function font() {
			var fonts = document.getElementsByName('font');
			for (var i = 0, length = fonts.length; i < length; i++) {
				if (fonts[i].checked) {
					return fonts[i].value;
				}
			}
			return "";
		}

		function setFont() {
			var e = document.getElementsByName("font");
			for (var i = 0; i < e.length; i++) {
				if (e[i].value == "<?php echo $select_font?>") {
					e[i].checked = true;
					break;
				}
			}
			updateImage();
		}

		function validateUpload() {
			var file = document.getElementById("fileToUpload").value;
			var reg = /(.*?)\.(ttf|TTF|otf|OTF)$/;
			if(!file.match(reg)) {
				alert("You can only upload a TrueType or OpenType font (.ttf or .otf extension)");
				return false;
			}
		}

	</script>
</body>

</html>
