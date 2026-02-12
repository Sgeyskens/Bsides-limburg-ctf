from flask import Blueprint, render_template

def load(app):
    bp = Blueprint(
        "jumpscare",
        __name__,
        template_folder="templates",
        static_folder="assets",
        static_url_path="/plugins/jumpscare/assets",
    )
    app.register_blueprint(bp)
    
    @app.context_processor
    def inject_jumpscare():
        return {
            "enable_jumpscare": True
        }
