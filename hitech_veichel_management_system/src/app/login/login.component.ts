import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { VeichelserviceService } from '../services/veichelservice.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  loginForm: FormGroup;
  showPassword = false;
  passwordFieldType = 'password';

  constructor(private fb: FormBuilder, private router: Router,private allService: VeichelserviceService) {
    this.loginForm = this.fb.group({
      login_id: ['', Validators.required],
      user_password: ['', Validators.required],
    });
  }

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
    this.passwordFieldType = this.showPassword ? 'text' : 'password';
  }

  logIn() {
    if (this.loginForm.valid) {
      // Authentication logic goes here
      this.allService.login(this.loginForm).subscribe((data: any) => {
       
      if(data){
          // Save the token, role ID, user ID, organization ID, and navigations array in localStorage
          console.log(data.roles.toString());
          
          localStorage.setItem('authToken', data.token);
          localStorage.setItem('roleId', data.role.role_id.toString());
          localStorage.setItem('roleName', data.role.role_name.toString());
          localStorage.setItem('roleNames', data.roles.toString());

          localStorage.setItem('userInfo', JSON.stringify({
            userId: data.user.user_id,
            organizationId: data.organization.organization_id,
            navigations: data.navigations,
            user_name:data.user.user_name
          }));

 //Optionally navigate to the dashboard and reload the page
        this.router.navigate(['/dashboard']).then(() => {
          window.location.reload();
        });
       }else{
        alert("no data found!!")
       }
       
      } ,error => {
        alert('please check your password and user id');
      });
    } else {
      console.error('Form is invalid');
      alert("Invalid check again!!")
    }
  }
  
  }


