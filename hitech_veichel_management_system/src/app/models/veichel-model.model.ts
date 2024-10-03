export class VeichelModel {
}

export interface userDetails {
    user_id: number;
    role_id: number;
    user_organization_id: number;
    user_login_id: string;
    user_name: string;
    user_1st_mobile_no: string;
    user_2nd_mobile_no?: string;
    user_wp_no: string;
    doj: string;
    dob: string;
    gender: string;
    aadhar_no: string;
    address: string;
    photo: string;
    status: number;
    entry_by: number;
    created_at: string;
    updated_at: string;
    organization_by_user_id?: {
      organization_id: number;
      organization_name: string;
      organization_location: string;
      organization_inclusion_date: string;
      organization_status: number;
      entry_by: number;
      created_at: string;
      updated_at: string;
    };
    role?: {
      role_id: number;
      role_name: string;
      entry_by: string;
      created_at: string;
      updated_at: string;
    };
    assigned_role?: {
      assigned_role_id: number;
      user_id: number;
      role_id: number;
      entry_by: number;
      created_at: string;
      updated_at: string;
    };
  }

export class editFuelModel{
  fuel_expenses_id:any;
  filling_date:any;
  filling_amount:any;
  filling_quantity:any;
  last_km_reading:any;
  updated_by:any;
} 