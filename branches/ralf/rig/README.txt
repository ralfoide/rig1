// vim: set expandtab tabstop=4 shiftwidth=4: //
//**********************************************
// RIG version 1.0
// Copyright (c) 2002 Ralf
//**********************************************
// $Id$
//**********************************************


Summary

RIG is a web-based JPEG image album viewer, especially useful for digital camera albums; provides automatic image resizing, preview & thumbnail caching, user authentification; composed of a PHP web interface and a C++ thumbnail engine.

------

What is RIG?

RIG (a.k.a. the Ralf Image Gallery) is a web-based image album viewer.
The main application of RIG is a viewer for digital camera albums; as such it offers specific functionalities like automatic image resizing and handling of dated album names.

RIG is composed of two parts:
- a set of PHP scripts allowing a user to navigate albums and display images via a web interface,
- a C++ "thumbnail" engine that can resize images "on the fly". It is used to produce thumbnails as well as resize images to the viewer's preference.

The images are read directly from the local file system. The directories are interpreted as albums, each album containing an arbitrary number of images or sub-albums. Currently only JPEG files are supported as this is the most common format produced by digital cameras.

RIG currently works using PHP4 with Apache. It is mainly developped under Linux (Debian/Apache 1.3/Php4) but also tested under Win32/Apache/Php4. It is expected that the scripts will work with other web servers supporting PHP or at least require only a minimal set of modifications.

RIG thumbnail, the resizing application, is a C++ application that compiles with GCC (for Linux) or Visual Studio 6 (Win32). It is expected to be portable on other platforms using a reasonable C++-compatible compiler. The thumbnail has been designed with speed in mind yet code maintenance has not been sacrified for speed. 1600x1200 images are resized in less than 1 second on a not-so-high end Pentium III @ 1Ghz computer (usually in less time than is needed for the resulting image to be downloaded by the client web browser) or in 2~3 seconds on a K6-II @ 350 MHz (i.e. a low end DSL server).

RIG maintains a cache of resized images in order to produce them once. The web interface provides an administration page that can be used to pre-compute all the resized images. The administrator can also hide/show images or albums at will.

The web interface is composed of a small set of HTML and PHP modular scripts, which are expected to be easy to understand and modify even for a beginner or a non-programmer. All UI strings (for the web interface) are isolated for internationalization purposes and the user can choose between French and English (the default) in the web interface. Configuration files provide support for site-wide settings vs. album-wide settings. It is also possible to have different albums (with different configuration) served by the same server yet sharing the same php code base.

The current revision is 0.6.2. This version is stable, yet more features are expected to be added to the project before it can be labelled 1.0, such as album/image renaming or reorganisation, authentification and upload of new images using the web interface.

Version 0.6.2 has been available since novembre 2001:
http://www.powerpulsar.com/~ralf/rig/distrib/rig-0.6.2-011130.tgz
This version has been up and running since then on my public album server as well as some other friends' servers.
http://www.powerpulsar.com/~ralf/rig/index.php for an example.

------

What is the RIG License?

RIG 0.6.2 used to have a modified version of the Artistic License.
The license has been reverted to the pure Artistic License to be an OSD-Compliant Open Source license. *grin*
http://www.powerpulsar.com/~ralf/rig/LICENSE.html

------

R/

