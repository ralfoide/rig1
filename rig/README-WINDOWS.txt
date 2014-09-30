
PLEASE READ if you are building RIG-THUMBNAIL on Windows:

The unix makefile automatically downloads the JPEGLIB, expand the tar archive and patches it.
Otherwise, perform these steps:

0- If you have Cygwin for NT:

If you have cygwin installed, just run the script get-jpeglib.sh.
Then do
	cd rig/thumbnail/jpegsrc
	./get-jpeglib.sh
	tar xvzf jpegsrc.v6b.tar.gz


1- or download the JPEGLIB manually:

ftp://ftp.uu.net/graphics/jpeg/jpegsrc.v6b.tar.gz

Save it omewhere and unpack it in rig/thumbnail/jpegsrc
Winzip does that perfectly.
You must end up with a directory with the source located at:
rig/thumbnail/jpegsrc/jpeg-6b/


2- patch it:

Copy all the 5 files from rig/thumbnail/jpegsrc/ralf-patch
into rig/thumbnail/jpegsrc/jpeg-6b/. Override any existing files.


3- build it:

Open rig/thumbnail/jpegsrc/jpeg-6b/makelib.dsw with Visual Studio 6.0
(I haven't build the solution files for Visual Studio .NET yet opening
the 6.0 dsw should work, kind of).

Build it. Whichever target you want (Release prefered) just to make
sure it works.


4- build rig-thumbnail:

Open the rig/thumbnail/Thumbnail.dsw
The result should be an exe file created and called:
rig/thumbnail/Release/rig_thumbnail.exe

Enjoy
R/ 20020803


