// Bootstrap CSS imported by importmap
import 'bootstrap/dist/css/bootstrap.min.css';

// Bootstrap JS (should include popperjs as well) - alias is configured in importmap.php
import 'bootstrap';

// Font Awesome icons
import '@fortawesome/fontawesome-free';
import '@fortawesome/fontawesome-free/css/fontawesome.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Bootstrap Icons
import 'bootstrap-icons/font/bootstrap-icons.css';

// CSS scripts
import './styles/app.css';
import './styles/utils.css';

// JS scripts
import './js/app/BlockButton.js';
import './js/app/Dropdown.js';

// Stimulus
import './stimulus_bootstrap.js';

// Symfony Ux-React
import {registerReactControllerComponents} from '@symfony/ux-react';
registerReactControllerComponents();
