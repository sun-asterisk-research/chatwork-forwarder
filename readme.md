## Chatwork-forwarder

### About system
Slack Forwarder là hệ thống cho phép nhận payload từ service khác, sau đó dựa vào các config của
người dùng để tạo ra các message như mong muốn và gửi lên chatwork.

### Main features
* Phân tích thống kê dữ liệu
* Tạo webhook phù hợp với mục đích sử dụng
* Tạo message theo từng điều kiện cụ thể
* Sử dụng chatbot gửi message nhanh chóng lên chatwork.
* Lưu trữ lịch sử payload đã gửi đến và message đã gửi đi

### Local setup
    `$ composer install`
* using npm

    `$ npm install`

    `$ npm run dev`

* using yarn

    `$ yarn install`

    `$ yarn dev`

### How to deploy
Để deploy project chúng ta cần setup server gồm nginx, php-fpm, mysql sau đó thực hiện pull code và deploy như bình thường.
1. Setup nginx, php-fpm, mysql, composer, git, nodejs, npm or yarn.
2. Config domain.
3. Publish your project.

Ngoài ra bạn có thể deploy bằng docker với việc sử dụng dockerfile trong thư mục docker của project.

### Contribution
Vui lòng làm theo hướng dẫn sau để thực hiện contribute cho chatwork-forwarder.
1. **Fork** the repo on GitHub
2. **Clone** the project to your own machine
3. **Commit** changes to your own branch
4. **Push** your work back up to your fork`
5. Submit a **Pull request** to branch **develop** so that we can review your changes

Note: Hãy chắc chắn rằng bạn đã merge code mới nhất từ branch develop để tránh conflict.
