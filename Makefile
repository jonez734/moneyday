export HOST = falcon
export PROJECT = moneyday
export REPODIR = /srv/www/vhosts/projects.zoidtechnologies.com/files/$(PROJECT)/

export PROD = $(HOST):/srv/www/vhosts/www.zoidtechnologies.com/
export PRODDOCROOT = $(PROD)80/html/
export STAGEPROD = /srv/staging/www.zoidtechnologies.com/
export STAGEPRODDOCROOT = $(STAGEPROD)80/html/
export STAGEPRODSSLDOCROOT = $(STAGEPROD)443/html/

export DEV = $(HOST):/srv/www/vhosts/wwwdev.zoidtechnologies.com/
export STAGEDEV = /srv/staging/wwwdev.zoidtechnologies.com/
export STAGEDEVDOCROOT = $(STAGEDEV)80/html/

export datestamp = $(shell date +%Y%m%d-%H%M)
export archivename = $(PROJECT)-$(datestamp)-$(USER)
export installfile = install --mode=0660

export RSYNC = rsync --chmod=Dg=rwxs,Fgu=rw,Fo=r --no-times --verbose \
	--archive --update --backup --recursive \
	--human-readable --checksum --rsh=ssh \
	--no-owner --no-group \
	--delete-after \
	--links \
	--exclude '.~lock*' \
	--exclude '*~' \
	--exclude '443'

all:

clean:
	-rm *~
	$(MAKE) -C skin clean
	$(MAKE) -C php clean
	$(MAKE) -C smarty clean
	$(MAKE) -C js clean

dev:	export DOCROOT = $(STAGEDEVDOCROOT)
dev:	export STAGE = $(STAGEDEV)
dev:	
	mkdir -p $(DOCROOT)
	mkdir -p $(STAGE)templates_c/

	$(MAKE) -C php install
	$(MAKE) -C skin install
#	$(MAKE) -C smarty install
#	$(MAKE) -C js install
#	$(installfile) config-dev.php $(DOCROOT)config.php
#	-$(installfile) htpasswd-dev $(DOCROOT).htpasswd
#	$(installfile) htaccess-dev $(DOCROOT).htaccess
	$(RSYNC) $(STAGE) $(DEV)

prod:	export DOCROOT = $(STAGEPRODDOCROOT)
prod:	export STAGE = $(STAGEPROD)
prod:
	areyousure || (echo "aborted $$?"; exit 1)
	mkdir -p $(DOCROOT)
	mkdir -p $(STAGE)templates_c/
	#mkdir -p $(DOCROOT)captchas/ # for captcha images

#	$(MAKE) -C .. prod
	$(MAKE) -C php install
	$(MAKE) -C skin install
	#$(MAKE) -C smarty install
	#$(MAKE) -C js install
	#$(installfile) config-prod.php $(DOCROOT)config.php
	#$(installfile) ../private/htpasswd-prod $(DOCROOT).htpasswd
	#$(installfile) htaccess-prod $(DOCROOT).htaccess
	#$(installfile) google067c1cb56376e896.html $(DOCROOT)google067c1cb56376e896.html
	$(RSYNC) $(STAGE) $(PROD)

.PHONY: prod dev
