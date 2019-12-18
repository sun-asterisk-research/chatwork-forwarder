PREFIX ?= registry.gitlab.com/sun-asterisk-research/pages

all:
	bash .env && docker build . -t $(PREFIX)/chatwork-forwarder:latest \
		-f docker/Dockerfile \
		--build-arg APP_URL=$$APP_URL

push:
	docker push $(PREFIX)/chatwork-forwarder:latest

clean:
	docker rmi $(PREFIX)/chatwork-forwarder:latest
