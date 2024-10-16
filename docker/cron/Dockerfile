# docker/cron/Dockerfile
FROM php:8.0-cli

# Install cron
RUN apt-get update && apt-get install -y cron

# Copy the application code
COPY . /app
WORKDIR /app

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Create the log file
RUN touch /var/log/worker.log


# Copy worker-cron file to the cron.d directory
COPY docker/cron/worker-cron /etc/cron.d/my-cron-job

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/my-cron-job

# Apply the cron job
RUN crontab /etc/cron.d/my-cron-job

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Run the command on container startup
CMD cron && tail -f /var/log/cron.log
