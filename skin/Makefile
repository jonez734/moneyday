all:

clean:
	-rm *~
	#$(MAKE) -C css clean
	$(MAKE) -C tmpl clean
	#$(MAKE) -C art clean

install:
	mkdir -p $(DOCROOT)skin/tmpl/
	#mkdir -p $(DOCROOT)skin/scss/
	#mkdir -p $(DOCROOT)skin/art/
	#$(MAKE) -C scss install
	$(MAKE) -C tmpl install
	#$(MAKE) -C art install
