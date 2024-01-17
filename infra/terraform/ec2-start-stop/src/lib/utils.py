from pathlib import Path

from jinja2 import Environment, FileSystemLoader
from . import setup_logger

logger = setup_logger(label=__name__)


def render_template(template_vars: dict):
    """ Render a report template """

    templatedir = Path(__file__).parent.parent / 'templates'
    j2_env = Environment(loader=FileSystemLoader(templatedir), autoescape=True)
    template = j2_env.get_template('report.html.j2')

    logger.info('generating report...')

    return template.render(template_vars)
