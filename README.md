# truetype2gfx

### Converting fonts from TrueType to Adafruit GFX

Many Arduino projects and ready-built devices come with a display. And
the Adafruit GFX display driver is used by many of them to display
variable-width fonts. Some fonts usually are included with the driver,
and then there's a complicated procedure for adding your own fonts. It
involves compiling tools and a trial-and-error process for figuring
out how big the font will be on your display as well as relative to
the other fonts.

But now you can skip all that and convert the fonts your Arduino
project needs with ease. No need to compile tools, no need to find out
how big a font will be by trial and error. Simply select a FreeFont or
upload any TrueType or OpenType font, select a size, specify the glyphs
you want, download the include file and you're ready to use the font
in your project.

### [Click here if you just want to use truetype2gfx](https://gfx.stfw.org)

This is the github repository. The tool itself is a server thing that
works with your browser. It is publically available no need to install
anything, [just click](https://gfx.stfw.org). That webpage has the
tool and all the information needed to use it.

This repository has the PHP/Javascript source and documents how to
install it if you want to run a copy on your own server, or just see
how it was done.

### Issues, requests, help

If you open an issue on this repository, I'll see what I can do.

### Running your own copy

If you want to run your own server because:

 * You want to tweak something
 * You're working with s00per seekrit font
 * or some other reason

.. here's how:

1. Copy the files from this repository to a directory on a server that
   has PHP enabled. You will need support for `gd` and `freetype`
   enabled in the PHP installation, check with `phpinfo()` to see if
   they are there.

2. In this directory, add a compiled version my fork of the Adafruit
   `fontconvert` tool (see
   [here](https://github.com/charles-haynes/fontconvert)) and make
   sure it it executable by the user that runs your webserver.

3. Make sure the fonts/user directory is writable for the webserver
   user.

This tool is basically a clone of https://github.com/ropg/truetype2gfx
with a few tweaks. All the credit should go to it's author <a
href="https://github.com/ropg">Rop Gonggrijp</a> who is also the
author of the awesome <a
href="https://github.com/ropg/ezTime">ezTime</a> Arduino date and time
library.
