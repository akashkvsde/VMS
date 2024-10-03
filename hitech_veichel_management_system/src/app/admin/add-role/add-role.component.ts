
import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

declare var bootstrap: any;

@Component({
  selector: 'app-add-role',
  templateUrl: './add-role.component.html',
  styleUrls: ['./add-role.component.css']
})
export class AddRoleComponent implements OnInit {
  addRole: any = {};
  rolesForm!: FormGroup;
  roles: any[] = [];
  editMode: boolean = false;
  selectedRoleId: any | null = null;
  entry_by: any;
  selectedRole: any = null;
  isModalOpen = false;

  @ViewChild('exampleModal', { static: false }) exampleModal!: ElementRef;

  constructor(private fb: FormBuilder, private vehicleService: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.entry_by = userInfo.userId;
    
    this.rolesForm = this.fb.group({
      role_name: ['', Validators.required],
    });
    this.getRoles();
  }

  onSubmit(form: any): void {
    if (form.valid) {
      const formData = new FormData();
      formData.append('role_name', this.addRole.role_name || '');
      formData.append('entry_by', this.entry_by);

      this.vehicleService.addRole(formData).subscribe(
        (response: any) => {
          alert('Role added successfully');
          form.reset();
          this.getRoles();
        },
        (error: any) => {
          alert('Role not added');
          console.log(error);
        }
      );
    }
  }

  getRoles() {
    this.vehicleService.getRole().subscribe((res: any) => {
      this.roles = res;
      // console.log(this.roles);
    });
  }

  editRole(roles: any): void {
    this.editMode = true;
    this.selectedRoleId = roles.role_id;
    this.rolesForm.patchValue({
      role_name: roles.role_name,
    });
  }

 

  openModal(role: any) {
    this.selectedRole = role;
    this.rolesForm.patchValue(role);
    this.isModalOpen = true;
  }

  closeModal() {
    this.isModalOpen = false;
    this.rolesForm.reset();
    this.selectedRole = null;
  }

  onUpdate() {
    if (this.rolesForm.valid) {
      const updatedRole = {
        ...this.selectedRole,
        ...this.rolesForm.value
      };

      this.vehicleService.updateRole(this.selectedRole.role_id, updatedRole).subscribe(
        (response: any) => {
          alert('Role updated successfully');
          this.getRoles(); // Refresh the list of roles
          this.closeModal();
        },
        (error: any) => {
          alert('Failed to update role');
        }
      );
    }
  }
  
}