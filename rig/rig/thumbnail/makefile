#simplified makefile for Thumbnail for Linux
#creates ./thumbnail.exe

# -----------------------------
#  setup basic compilation flags

### DEBUG ##CFLAGS	= -O3
CFLAGS	= -g
LDFLAGS = 
LDLIBS  = -lstdc++ -lm -lc
LN      = $(CC)

INCDIR  = -I.

# ----- jpeglib support -------

ifneq ($(wildcard /usr/include/jpeglib.h*),)
	# found a jpeglib header, using -ljpeg from your system.)
	JPEGRULE =
	LIBJPEG  = -ljpeg
else
	# jpeglib header not found, building from source
	JPEGRULE = libjpeg
	JPEG_TGZ = jpegsrc.v6b.tar.gz
	DIRJPEG1 = ./jpegsrc
	DIRJPEG2 = $(DIRJPEG1)/jpeg-6b
	DIRJPEG3 = $(DIRJPEG1)/ralf_patch
	LIBJPEG  = $(DIRJPEG2)/libjpeg.a
	INCDIR  := $(INCDIR) -I$(DIRJPEG2)
endif

# ----- avifile support -------

ifneq ($(shell which avifile-config),)
	# we found avifile-config. assume the system has libavifile-dev installed.
	USES_AVIFILE = 1
	LDLIBS:= $(LDLIBS) $(shell avifile-config --libs)
	INCDIR:= $(INCDIR) $(shell avifile-config --cflags)
	CFLAGS:= $(CFLAGS) -DRIG_USES_AVIFILE
endif	

# ----- libavcodec/libavformat support -------

ifneq ($(wildcard /usr/include/ffmpeg/*),)
	# we found the main headers for ffmpeg; assume libavformat & libavcodec are present
	USES_AVCODEC = 1
	LDLIBS:= $(LDLIBS) -lavformat -lavcodec
	INCDIR:= $(INCDIR) -I/usr/include/ffmpeg
	CFLAGS:= $(CFLAGS) -DRIG_USES_AVCODEC -D__STDC_CONSTANT_MACROS
endif

# -----------------------------

CFILES = rig_thumbnail.cpp rig_rgb.cpp rig_jpeg.cpp rig_avifile.cpp rig_avcodec.cpp

OFILES = $(patsubst %.cpp,%.o,$(CFILES))

TARGET = rig_thumbnail.exe

# -----------------------------

all: info $(JPEGRULE) $(TARGET)
	@echo "Done."

# -----------------------------

libjpeg:
	$(MAKE) -f makefile-libjpeg

# -----------------------------

$(TARGET): $(OFILES)
	$(LN) $(LDFLAGS) -o $(TARGET) $(OFILES) $(LIBJPEG) $(LDLIBS)

$(OFILES): %.o: %.cpp
	$(CC) -c $(CFLAGS) $(INCDIR) $< -o $@

# -----------------------------

ALL_OFILES = $(wildcard *.o)
ALL_EFILES = $(wildcard *.exe)

clean: clean-o check-all-clean

clean-o:
	rm -fv $(ALL_OFILES)
	@$(MAKE) -f makefile-libjpeg clean

check-all-clean:
	@if [ "x$(ALL_EFILES)" != "x" ]; then \
		echo; \
		echo "**** IMPORTANT: To remove $(ALL_EFILES) too, use 'make clean-all' *****"; \
		echo; \
	fi

clean-all: clean-o
	rm -fv $(ALL_EFILES)

super-clean: clean-all

extra-clean: clean-all


# -----------------------------

info:
	@if [ "x$(JPEGRULE)" = "x" ]; then \
		echo "**** LIBJPEG: Using -ljpeg from your system."; \
	else \
		echo "**** LIBJPEG: No jpeglib.h found, building libjpeg from source."; \
	fi
	@if [ "x$(USES_AVIFILE)" = "x" ]; then \
		echo "**** AVIFILE: No headers found for libavifile. Support not included."; \
	else \
		echo "**** AVIFILE: Some avifile headers found, trying to build with libavifile."; \
	fi
	@if [ "x$(USES_AVCODEC)" = "x" ]; then \
		echo "**** AVCODEC: No headers found for libavformat/libavcodec. Support not included."; \
	else \
		echo "**** AVCODEC: Some ffmpeg headers found, trying to build with libavformat/libavcodec."; \
	fi


# -----------------------------

#
# $Log$
# Revision 1.6  2003/11/25 05:03:30  ralfoide
# Using Gnu Make builtin MAKE variable instead of hardcoded make name.
# Allows for script to run with -j n for multi processor support.
#
# Revision 1.5  2003/11/09 20:53:30  ralfoide
# Added clean-all target to remove exe too
#
# Revision 1.4  2003/08/16 22:04:02  ralfoide
# Always compile its own libjpeg.
#
# Revision 1.3  2003/07/16 07:04:59  ralfoide
# Separating with-video/no-video makefiles
#
#
