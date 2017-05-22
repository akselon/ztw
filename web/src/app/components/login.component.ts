import {Component} from '@angular/core';
import {Router} from '@angular/router';
import {AuthenticationService} from '../services/authentication.service'
import {User} from '../models/user';

@Component({
    selector: 'login-form',
    providers: [AuthenticationService],
    templateUrl: './assets/login.component.html',
    styles: [`
        @media screen and (min-width: 768px) {
            md-card {
                margin: 15px;
            }
        }
        md-card { padding: 0; }
        .tab-container { padding: 20px; }
        .http-status {
            padding: 5px;
            font-weight: 500;
        }
        .http-status.error {
            color: #ff0000;
        }
    `]
})

export class LoginComponent {
    public user = new User(0, '', '', '', '', '');
    public httpLoginStatusMessage = '';
    public httpLoginStatusError = false;

    public httpRegisterStatusMessage = '';
    public httpRegisterStatusError = false;
    public newUser = {login: '', email: '', password1: '', password2: ''};

    constructor(
        private router: Router,
        private service: AuthenticationService) {}

    login() {
        this.httpLoginStatusError = false;
        this.httpLoginStatusMessage = 'Logging in ...';
        this.service.login(this.user.login, this.user.password).then(res => {
            if (res.ok) {
                this.httpLoginStatusError = false;
                this.httpLoginStatusMessage = 'Logged in succesfully <md-icon></md-icon>';
                setTimeout(() => {
                    this.router.navigate(['/']);
                }, 3000);
            } else {
                this.httpLoginStatusError = true;
                this.httpLoginStatusMessage = 'Could not log in: ' + res.error_msg;
            }
        });
    }

    register() {
        this.httpRegisterStatusError = false;
        this.httpRegisterStatusMessage = 'Registration ...';
        this.service.register(this.newUser.login, this.newUser.email, this.newUser.password1, this.newUser.password2).then(res => {
            if (res.ok) {
                this.httpRegisterStatusError = false;
                this.httpRegisterStatusMessage = 'Registered succesfully! Logging in...';
                setTimeout(() => {
                    this.router.navigate(['/home']);
                }, 1000);
            } else {
                this.httpRegisterStatusError = true;
                this.httpRegisterStatusMessage = 'Could not register: ' + res.error_msg;
            }
        });
    }
}