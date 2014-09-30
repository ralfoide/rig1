#simplified makefile for Thumbnail for Linux
#creates ./thumbnail.exe

# -----------------------------

OFILES = $(wildcard *.o)
EFILES = $(wildcard *.exe)

# -----------------------------

all:
	@echo
	@echo "Please select target 'no-video' or target 'with-video' by using"
	@echo "	make no-video"
	@echo "or"
	@echo "	make with-video"
	@echo
	@echo "The makefile will use 'wget' to download libjpeg.6b"
	@echo "To compile the video version, you need to install libavifile-devel 0.7.33 first"
	@echo

no-video:
	$(MAKE) -f makefile-no-video

with-video:
	$(MAKE) -f makefile-with-video

# -----------------------------

clean:
	rm -fv $(OFILES)
	$(MAKE) -f makefile-libjpeg clean
	@echo
	@echo "**** IMPORTANT: To remove $(EFILES) too, use 'make clean-all' *****"
	@echo

clean-all: clean
	rm -fv $(EFILES)

super-clean: clean-all

extra-clean: clean-all


# -----------------------------

#
# $Log: makefile,v $
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
