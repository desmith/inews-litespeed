import boto3

from . import setup_logger

logger = setup_logger(label=__name__)


def send_email(
    sender: str = None,
    subject: str = None,
    html: str = None,
    text: str = None,
    recipient: list = None,
):
    # Provide the contents of the email.
    charset = 'UTF-8'

    ses = boto3.client("ses")

    response = ses.send_email(
        # Destination={"ToAddresses": to, "CcAddresses": cc},
        # Destination={"ToAddresses": [recipient]},
        Destination={"ToAddresses": recipient},
        Message={
            "Subject": {"Charset": charset, "Data": subject},
            "Body": {
                "Html": {"Charset": charset, "Data": html},
                "Text": {"Charset": charset, "Data": text},
            },
        },
        Source=sender,
        Tags=[
            {"Name": "Name", "Value": "report-awsbackup-status"},
            {"Name": "app", "Value": "reports"},
        ],
    )

    logger.info('%s was sent to %s.', subject, recipient)

    return response
