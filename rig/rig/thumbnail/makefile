# Simplified makefile for Thumbnail for Linux
# Creates ./thumbnail.exe
# 
# Note RM 20030628:
# libjpeg is no longer automatically compiled here, instead this relies on the
# existance of -ljpeg (i.e. please install libjpeg using RPM or APT first!)
#
# If you don't have libjpeg on your system, try this:
# make -f makefile-with-libjpeg

CC=gcc
CFLAGS= -O
LDFLAGS= 
LDLIBS:= -lstdc++ -lm -lc -ljpeg
LN= $(CC)

INCDIR:= -I. 

# ----- avifile support -------

LDLIBS:= $(LDLIBS) $(shell avifile-config --libs)
INCDIR:= $(INCDIR) $(shell avifile-config --cflags)

# -----------------------------

CFILES=rig_thumbnail.cpp rig_rgb.cpp rig_jpeg.cpp rig_avifile.cpp

OFILES=$(patsubst %.cpp,%.o,$(CFILES))

TARGET=rig_thumbnail.exe

all: $(TARGET)

$(TARGET): $(OFILES)
	$(LN) $(LDFLAGS) -o $(TARGET) $(OFILES) $(LDLIBS)

# avitype.exe: avitype.o
# 	$(LN) $(LDFLAGS) -o avitype.exe avitype.o $(LDLIBS)

$(OFILES): %.o: %.cpp
	$(CC) -c $(CFLAGS) $(INCDIR) $< -o $@

clean:
	rm -fv $(OFILES)

# --- experimental ---

rig_exif: rig_exif.c
	gcc $(INCDIR) rig_exif.c -o rig_exif


#
# $Log$
# Revision 1.2  2003/06/30 06:06:32  ralfoide
# No longer makes libjpeg, instead use libjpeg and libavifile installed in system.
#
# Revision 1.1  2002/08/04 00:58:08  ralfoide
# Uploading 0.6.2 on sourceforge.rig-thumbnail
#
# Revision 1.1  2001/11/26 00:07:40  ralf
# Starting version 0.6: location and split of site vs album files
#
#
