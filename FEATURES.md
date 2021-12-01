**Slack Forwarder** is Opensource was developed with Laravel framwork, which help your project solve the following probems.

1. Send message to Chatwork (private or room)
2. Receive payload from other service and extract data with users's conditions.
3. Mapping data befor sending with user-friendly and easy to understand.
4. Send multiple messages at the same time for multiple rooms or private, depending on the user's configuration.

If your project is working with chatwork, you need to process the sending of the message when input data is received or simply happens every day when each member in the project sends a Pull Request they have to TO on the project box to request review merge Pull, this work is repetitive, both boring and time-consuming. Slack Forwarder will help your project solve those problems extremely quickly, just need a few easy config steps.

Let's go!
## 1. Landing page and login
Enter the url  [https://cw-forwarder.sun-asterisk.vn/](https://cw-forwarder.sun-asterisk.vn/). This is chatwork-forwarder's landing. We must login with click login button before continue.
![](https://images.viblo.asia/17cef2a1-4f85-468b-ae32-8e5cf78854d8.png)

## 2. Dashboard
After login, we will see Dashboard page that is showing statistics:
1. Number of webhooks created.
2. Number of Payloads received.
3. Number of messages sent
4. Number of bots in use.

![](https://images.viblo.asia/f80cd0d6-f8d1-4b8f-8062-18e52de3564e.png)

## 3. Bot
This is section that manages the addition and modification of chatwork's bot. As we know, sending message to the chatwork we must have a chatwork account as a Bot, and have to get its API token.

![](https://images.viblo.asia/8334a8e2-74f7-4747-beed-a182e48b81b4.png)


> Bot key is Bot-Account's API-token, you can go to API setting in chatwork to get it.

## 4. Webhook
The webhook list screen is the created webhooks, here we can create new webhook or edit an existing webhook.

For example, I will create a webhook for project XYZ to send a message every time a member sends a Pull Request. We will use the bot that created at the beginning and send to myself (If you want to sent to the room you can choose the room type of Group instead of Private).
![](https://images.viblo.asia/4f096d38-c040-4d3c-8ba9-cfd18fb14e27.png)


After **save** we will redirect to `edit webhook` page. Here we will config `payloads`, `conditions` and `mapping`.
![](https://images.viblo.asia/0e3e5478-b8f9-449c-affd-666fedb7fdb6.jpg)


`URL` is webhook that created to give the services which we want to receive payload. We need copy this url and go to the webhook settings on the project github (Only project's admin can access to this seting).

![](https://images.viblo.asia/bc05c6a5-0c7f-4b57-87f1-0a21dd326294.png)

> The `Content type` default is `applicationi/x-www-form-urlencoded`, we need change this to `application/json`.

Receiving the payload when has some one who send Pull Request.
![](https://images.viblo.asia/a74ec04b-9997-4852-9268-f04c869bae4a.png)


Enter **Update webhook** to finish.

## 5. Payload
This is the most important, that help you config payload, conditions and content to send to chatwork. In each section we have an example (blue color).
![](https://images.viblo.asia/289395d8-6fd1-436e-a35d-db59a002b8e9.png)
![](https://images.viblo.asia/7b231cf7-267a-43c2-a000-9b0cb043f0c8.png)


To get payload pattern, we go to repo's setting  again, in `Recent Deliveries` we can see our payload, and copy it.
![](https://images.viblo.asia/9f402f8c-3560-4175-bc72-31f2d36b994e.jpg)

After copying the sample payload part of a PR, we will paste it into the input Payload params. The condition and content sections we can follow the example so that the system can find and read the data that you want to get in the pasted payload above.
For example:
![](https://images.viblo.asia/e7bc15e0-af66-47b3-94e9-e1e3f8d6cb1c.png)

> Note: we can user prefix `$params` or not.

After that, we `save` and wait for new PR, and this is result:

![](https://images.viblo.asia/f09e17cf-9716-4fa6-b7b5-5b5cda93634b.png)

## 6. Mapping
This is the part that helps you convert data from one value to another, understand that as the above example we have a user_id is 38203330, but you do not want to display it like that, instead 38203330 will display Thi is Quach Dai Phuc. Simply configure in the mapping section as follows:

![](https://images.viblo.asia/eb4d7daa-a0db-47e2-a67b-3b7f134fcac4.png)

In the content of the payload we can also configure which time the mapping value.

> Keeping original value: **{!! $params.user.display_name !!}**
>
> Mapping value with other: **{{$params.user.display_name}}**

That all, that's how chatwork-forwarder is used, hopefully it will be of great help in your project.
Any one can contribute here :

Github  [https://github.com/sun-asterisk-research/chatwork-forwarder ](https://github.com/sun-asterisk-research/chatwork-forwarder )
