import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class RouteGuard implements CanActivate {

  constructor(private router: Router) {}

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): boolean {
    // Retrieve user navigation data from session storage
    const userInfoString = localStorage.getItem('userInfo');
    const userInfo = userInfoString ? JSON.parse(userInfoString) : null;

    // Get the current URL
    const url = state.url;

    // Always allow access to dashboard and landing page
    if (url === '/dashboard' || url === '/dashboard/landing-page') {
      return true;
    }

    // If user info is missing, redirect to login
    if (!userInfo) {
      this.router.navigate(['/login']);
      return false;
    }

    // Check if the URL is in the user's allowed navigation list
    const isAllowed = userInfo.navigations.some((nav: any) => url.startsWith(nav.nav_url));

    if (isAllowed) {
      return true;
    } else {
      // If URL is not allowed, redirect to 404 or error page
      this.router.navigate(['/dashboard/landing-page']);
      return false;
    }
  }

  
}
