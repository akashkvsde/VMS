import { Component,OnInit} from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { VeichelserviceService } from 'src/app/services/veichelservice.service';

@Component({
  selector: 'app-vehicle-categories',
  templateUrl: './vehicle-categories.component.html',
  styleUrls: ['./vehicle-categories.component.css']
})
export class VehicleCategoriesComponent implements OnInit {

  categoryForm!: FormGroup;
  entry_by: any;
  organization_id: any;
  editMode = false;
  selectedCategory: any = null;
  categories: any[] = [];

  constructor(private formBuilder: FormBuilder, private allService: VeichelserviceService) {}

  ngOnInit(): void {
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;
    this.organization_id = userInfo?.organizationId;
    this.entry_by = userInfo?.userId;

    this.initializeForm();
    this.defaultData();
  }

  initializeForm() {
    this.categoryForm = this.formBuilder.group({
      vehicle_category_name: ['', [Validators.required, Validators.pattern('^[A-Za-z ]+$')]],
      entry_by: [this.entry_by]
    });
  }

  defaultData(): void {
    this.allService.getVehicleCategories().subscribe((data: any) => {
      this.categories = data;
    });
  }

  addNewCategory() {
    this.editMode = false;
    this.resetForm();
    // If you were using a modal, you would show it here
  }

  onSubmit() {
    if (this.categoryForm.valid) {
      const formData = this.categoryForm.value;

      if (this.editMode) {
        // Update existing category
        this.allService.updateVehiclecatagories(this.selectedCategory.vehicle_category_id, formData).subscribe((response: any) => {
          alert(response.message);
          this.defaultData();
        }, (err: any) => {
          console.log(err);
        });
      } else {
        // Add new category
        this.allService.addCategory(formData).subscribe((response: any) => {
          alert(response.message);
          this.defaultData();
        }, (err: any) => {
          console.log(err);
        });
      }

      // Reset form after submission
      this.resetForm();
    }
  }

  editCategory(category: any) {
    this.selectedCategory = category;
    this.editMode = true;
    this.categoryForm.patchValue(category);
    // Show modal manually for editing if using a modal

     // Scroll to the element using ElementRef
        // Scroll to the form section
  const formSection = document.getElementById('con');
  if (formSection) {
    formSection.scrollIntoView({ behavior: 'smooth' });
  }
  }

  resetForm() {
    this.categoryForm.reset({ entry_by: this.entry_by });
    this.editMode = false;
    this.selectedCategory = null;
  }
  
}
