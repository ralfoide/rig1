#simplified makefile for Thumbnail for Linux
#creates ./thumbnail.exe

# -----------------------------

OFILES = $(wildcard *.o) $(wildcard *.exe)

# -----------------------------

all:
	@echo "Please select target 'no-video' or target 'with-video'"

no-video:
	make -f makefile-no-video

with-video:
	make -f makefile-with-video

# -----------------------------

clean:
	rm -fv $(OFILES)

# -----------------------------

#
# $Log$
# Revision 1.3  2003/07/16 07:04:59  ralfoide
# Separating with-video/no-video makefiles
#
#
