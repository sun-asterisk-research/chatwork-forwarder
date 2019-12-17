PREFIX ?= registry.gitlab.com/sun-asterisk-research/pages

all:
	bash .env && docker build . -t $(PREFIX)/chatwork-forwarder:latest \
		-f docker/Dockerfile \
		--build-arg APP_URL=$$APP_URL \
		--build-arg GOOGLE_CLIENT_ID=$$GOOGLE_CLIENT_ID \
		--build-arg GOOGLE_CLIENT_SECRET=$$GOOGLE_CLIENT_SECRET \
		--build-arg GOOGLE_REDIRECT=$$GOOGLE_REDIRECT

push:
	docker push $(PREFIX)/chatwork-forwarder:latest

clean:
	docker rmi $(PREFIX)/chatwork-forwarder:latest
