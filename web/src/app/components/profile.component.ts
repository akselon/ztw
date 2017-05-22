import {Component, OnInit} from '@angular/core';
import {AuthenticationService} from '../services/authentication.service';
import {ActivatedRoute, Router} from '@angular/router';
import {MdDialog} from '@angular/material';
import {UserService} from '../services/user.service';
import {User} from '../models/user';

@Component({
    selector: 'login-form',
    providers: [AuthenticationService],
    templateUrl: './assets/profile.component.html',
    styleUrls: [ './assets/common.component.css'],
    styles: [`
        md-input-container {display: block !important;}
    `]
})
export class ProfileComponent implements OnInit {
    user: User;
    isLoading = true;
    showWarning = false;
    warningMessage = '';

    constructor(
        private router: Router,
        private route: ActivatedRoute,
        private authService: AuthenticationService,
        private userService: UserService,
        public dialog: MdDialog) {}
    logout() {
        this.authService.logout().then(res => {
            this.router.navigate(['/login']);
        });
    }
    saveChanges() {
        this.userService.saveChanges(this.user).then(result => {
            this.dialog.open(ChangeSettingsDialog);
        });
    }
    ngOnInit() {
        this.userService.getUserSettings()
            .then(user => {
                this.user = user;
                this.isLoading = false;
            })
            .catch(message => {
                this.warningMessage = message;
                this.showWarning = true;
                this.isLoading = false;
            });
    }
}

@Component({
    selector: 'change-settings-dialog',
    templateUrl: './assets/change-settings-dialog.html',
})
export class ChangeSettingsDialog {}
