**English** · [Français](README.fr.md)

# Ligue 1 Project

## Description

Ligue 1 is a PHP project for managing data about the French Ligue 1 football league. It uses an MVC (Model-View-Controller) architecture to keep the code organised and make it easier to evolve.

It is designed to run locally with XAMPP and includes user interfaces, database management and dynamic navigation through a custom router.

## Project structure

```
Ligue1/
|-- avatars/          # User avatars
|-- config/           # Project configuration (settings files)
|-- Controleur/       # Controllers (application logic)
|-- img/              # Static images
|-- logs/             # Error and activity logs
|-- Models/           # Models (data handling and database access)
|-- nbproject/        # NetBeans project files (if applicable)
|-- style/            # Stylesheets (CSS)
|-- Vues/             # Views (user interfaces)
|-- .htaccess         # Apache URL rewriting
|-- check_email.php   # Email validation
|-- footer.php        # Shared footer
|-- index.php         # Main entry point
|-- logout.php        # Logout handling
|-- menu.php          # Shared navigation menu
|-- Router.php        # Routing
|-- utils.php         # Shared helper functions
```

## Main features

- **Email validation**:
  `check_email.php` checks the emails submitted by users.

- **Session handling**:
  `logout.php` logs a user out securely.

- **Navigation**:
  `Router.php` dispatches requests to the right controllers and actions.

- **MVC architecture**, split into three layers:
  - **Model** (`Models/`): database access.
  - **View** (`Vues/`): user interfaces.
  - **Controller** (`Controleur/`): application logic, linking models and views.

- **Logging**:
  the `logs/` folder stores log files to track errors and events.

## Requirements

- **Web server**: Apache (included in XAMPP)
- **PHP**: 7.4 or higher
- **Database**: MySQL (included in XAMPP)
- **Browser**: any recent browser (Chrome, Firefox, Edge, etc.)

## Installation

1. Clone this repository into XAMPP's `htdocs` folder:
   ```bash
   git clone https://github.com/lburdier/ligue1.git
   ```

2. Import the database with the SQL file provided with the project:
   ```bash
   mysql -u root -p < createLigue1_v2.sql
   ```

3. Start Apache and MySQL from XAMPP.

4. Open the app in your browser:
   ```
   http://localhost/ligue1
   ```

## Contributing

Contributions are welcome:

1. Fork this repository.
2. Create a branch for your feature:
   ```bash
   git checkout -b my-feature
   ```
3. Open a pull request.

## Author

Built by Lucas Burdier, as a PHP application for managing sports data.

## License

This project is licensed under the [MIT](LICENSE) license.
