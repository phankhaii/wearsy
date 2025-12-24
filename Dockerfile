FROM php:8.2-apache

# Cài đặt extension mysqli để kết nối database
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Xóa các file rác (nếu cần thiết, giữ nguyên lệnh cũ của bạn)
RUN rm -rf /var/www/html/*

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80
