import { Component, HostListener } from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'hitech_veichel_management_system';
  constructor(){
    // console.clear();
  }

  // @HostListener('window:keydown', ['$event'])
  // handleKeyboardEvent(event: KeyboardEvent): void {
  //   switch (event.key) {
  //     case 'F12':
  //     case 'I':
  //       if (event.ctrlKey && event.shiftKey) {
  //         event.preventDefault();
  //       }
  //       break;
  //     case 'J':
  //       if (event.ctrlKey && event.shiftKey) {
  //         event.preventDefault();
  //       }
  //       break;
  //     case 'K':
  //       if (event.ctrlKey && event.shiftKey) {
  //         event.preventDefault();
  //       }
  //       break;
  //     case 'C':
  //       if (event.metaKey || event.ctrlKey) {
  //         event.preventDefault();
  //       }
  //       break;
  //     // Preventing Ctrl + U (View Source)
  //     case 'U':
  //       if (event.ctrlKey) {
  //         event.preventDefault();
  //       }
  //       break;
  //     // Preventing Ctrl + Shift + C (Inspect Element)
  //     case 'C':
  //       if (event.ctrlKey && event.shiftKey) {
  //         event.preventDefault();
  //       }
  //       break;
  //     // Preventing Cmd + Option + C for Mac users
  //     case 'C':
  //       if (event.metaKey && event.altKey) {
  //         event.preventDefault();
  //       }
  //       break;
  //     default:
  //       break;
  //   }
  // }

  // @HostListener('window:contextmenu', ['$event'])
  // handleContextMenu(event: MouseEvent): void {
  //   // Prevent right-click context menu
  //   event.preventDefault();
  // }

}
