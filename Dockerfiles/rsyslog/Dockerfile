FROM alpine:latest

# Install required packages and PHP extensions
RUN apk update && \
    apk upgrade && \
    apk add rsyslog && \
    mkdir -p /var/log/raisepon


COPY rsyslog.conf /etc/rsyslog.conf

CMD ["rsyslogd", "-n"]
