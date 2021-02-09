scssfiles := $(wildcard *.scss)
cssfiles := $(scssfiles:.scss=.css)

SCSS = sass --stop-on-error --trace --style expanded --sourcemap=none

all:

.scss.css:
	$(SCSS) $< $@

install: # $(cssfiles)
	echo $(DOCROOT)
	$(SCSS) --update --watch .:$(DOCROOT)skin/css/ # also try 'compressed'
	#-$(installfile) *.css $(STAGESTATICDOCROOT)skin/css/


clean:
	-rm *~ *.css *.css.map

.SUFFIXES: .scss .css
