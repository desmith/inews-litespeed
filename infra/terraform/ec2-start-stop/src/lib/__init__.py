import logging
import sys


def setup_logger(label: str = None):
    _logger = logging.getLogger(label)
    log_level = logging.INFO
    _logger.setLevel(log_level)

    ch = logging.StreamHandler(sys.stderr)
    ch.setLevel(log_level)
    formatter = logging.Formatter(
        '[%(levelname)s] %(name)s:%(lineno)d %(funcName)s():%(message)s'
    )

    ch.setFormatter(formatter)
    _logger.addHandler(ch)

    return _logger
