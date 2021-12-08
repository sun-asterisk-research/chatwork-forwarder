REGISTRY_PATH ?= harbor.sun-asterisk.vn/rnd-internal/slack-forwarder
TAG ?= latest

LARAVEL_IMAGE=$(REGISTRY_PATH)/laravel-app:$(TAG)
WEB_IMAGE=$(REGISTRY_PATH)/web-app:$(TAG)
WEB_SERVER_IMAGE=$(REGISTRY_PATH)/web-server:$(TAG)

ifdef UNIQUE_TAG
WEB_SERVER_UNIQUE_IMAGE=$(REGISTRY_PATH)/web-server:$(UNIQUE_TAG)
endif

all:
	docker build . -t $(WEB_IMAGE) \
		-f docker/web.Dockerfile \
		--build-arg APP_URL=$(APP_URL) \
		--cache-from $(WEB_IMAGE)

	docker build . -t $(LARAVEL_IMAGE) \
		-f docker/laravel.Dockerfile \
		--cache-from $(LARAVEL_IMAGE)

	docker build . -t $(WEB_SERVER_IMAGE) \
		-f docker/Dockerfile \
        --build-arg REGISTRY_PATH=$(REGISTRY_PATH) \
		--build-arg TAG=$(TAG) \
		--cache-from $(WEB_IMAGE) \
		--cache-from $(LARAVEL_IMAGE)

pull:
	docker pull $(WEB_IMAGE) | true
	docker pull $(LARAVEL_IMAGE) | true
	docker pull $(WEB_SERVER_IMAGE) | true

release:
	docker push $(WEB_IMAGE)
	docker push $(LARAVEL_IMAGE)
	docker push $(WEB_SERVER_IMAGE)
ifdef WEB_SERVER_UNIQUE_IMAGE
	docker tag $(WEB_SERVER_IMAGE) $(WEB_SERVER_UNIQUE_IMAGE)
	docker push $(WEB_SERVER_UNIQUE_IMAGE)
endif

clean:
# Kill running containers
	docker ps -qf ancestor=$(WEB_SERVER_IMAGE) | xargs -r docker kill
# Also remove stopped ones
	docker ps -aqf ancestor=$(WEB_SERVER_IMAGE) | xargs -r docker rm
# Remove images
	docker images -q $(LARAVEL_IMAGE) | xargs -r docker rmi -f
	docker images -q $(WEB_IMAGE) | xargs -r docker rmi -f
	docker images -q $(WEB_SERVER_IMAGE) | xargs -r docker rmi -f
