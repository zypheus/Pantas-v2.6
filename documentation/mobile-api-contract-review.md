# Mobile API Contract Review

Date reviewed: 2026-07-09

## Scope

Reviewed `Pantas-UI/lib/services/*`, `Pantas-UI/lib/models/*`, and `pantas-v2.5` mobile routes/controllers under `/api/mobile`.

The goal was to verify whether the mobile app still matches the backend API after the backend moved mobile data sources to Library-domain tables.

## Summary

The current Pantas-UI service layer still matches the registered `/api/mobile` backend endpoints.

The backend now reads and writes Library-domain tables for catalog, borrowing, rooms, feedback, and notifications while preserving the response shapes that the Flutter models expect.

## Endpoint Matrix

| Pantas-UI service | Endpoint | Backend controller | Status |
| --- | --- | --- | --- |
| `AuthService.login` | `POST /login` | `Api\Mobile\AuthController@login` | Matches |
| `AuthService.logout` | `POST /logout` | `Api\Mobile\AuthController@logout` | Matches |
| `UserService.getCurrentUser` | `GET /profile` | `Api\Mobile\AuthController@me` | Matches |
| `UserService.updatePassword` | `POST /change-password` | `Api\Mobile\AuthController@changePassword` | Matches, but student-token login returns 409 |
| `UserService.submitFeedback` | `POST /feedback` | `Api\Mobile\FeedbackController@store` | Matches |
| `CatalogService.getHomeOverview` | `GET /home` | `Api\Mobile\AggregateController@home` | Matches |
| `CatalogService.getNewArrivals` | `GET /catalog/new-arrivals` | `Api\Mobile\CatalogController@newArrivals` | Matches |
| `CatalogService.searchCatalog` | `GET /catalog/search` | `Api\Mobile\CatalogController@search` | Matches |
| `CatalogService.getBookDetail` | `GET /catalog/books/{book}` | `Api\Mobile\CatalogController@book` | Matches |
| `CatalogService.getFilters` | `GET /catalog/filters` | `Api\Mobile\CatalogController@filters` | Matches |
| `BorrowService.getBorrowOverview` | `GET /borrow-overview` | `Api\Mobile\AggregateController@borrowOverview` | Matches |
| `BorrowService.submitCheckoutRequest` | `POST /borrow-cart/submit` | `Api\Mobile\BorrowingController@submitCart` | Matches |
| `RoomService.getDashboard` | `GET /rooms/dashboard` | `Api\Mobile\AggregateController@roomsDashboard` | Matches |
| `RoomService.getRooms` | `GET /rooms` | `Api\Mobile\RoomReservationController@rooms` | Matches |
| `RoomService.getAvailability` | `GET /rooms/availability` | `Api\Mobile\RoomReservationController@availability` | Matches |
| `RoomService.submitRoomReservation` | `POST /rooms/reservations` | `Api\Mobile\RoomReservationController@store` | Matches |
| `RoomService.getUserReservations` | `GET /rooms/reservations` | `Api\Mobile\RoomReservationController@index` | Matches |
| `RoomService.getReservationDetails` | `GET /rooms/reservations/{reservation}` | `Api\Mobile\RoomReservationController@show` | Matches |
| `RoomService.cancelReservation` | `DELETE /rooms/reservations/{reservation}` | `Api\Mobile\RoomReservationController@destroy` | Matches |
| `NotificationService.getNotifications` | `GET /notifications` | `Api\Mobile\NotificationController@index` | Matches |

## Findings

- Mobile login intentionally uses `student_id` only. Pantas-UI sends only `student_id`, so the contract matches.
- The backend authenticates mobile users as Library `Student` tokenables and resolves borrowing, rooms, feedback, and notifications through Library-domain tables.
- Catalog, borrow, room, notification, and feedback response fields line up with the Flutter model parsers.
- Pantas-UI has a registration screen, but it does not currently submit to a backend mobile registration API. It navigates locally after form completion. This is separate from the web `/register` workflow.
- `User.libraryQrCode` supports QR-related fields, but the backend mobile profile response does not currently include a QR code field. The current profile UI displays a placeholder QR icon and the student number, so this is not breaking today.
- `User.borrowingLimit` defaults to `0` because the profile response does not include `borrowing_limit`. Borrow limits are available through `/borrow-overview`, so this is not currently blocking the borrow screens.
- `ApiConfig.androidEmulator` is set to `http://127.0.0.1:8000/api/mobile`. For a normal Android emulator, this usually needs `http://10.0.2.2:8000/api/mobile` unless `adb reverse` or another tunnel is used.
- `ApiConfig.baseUrl` currently points to an ngrok URL. Local development should verify that the active tunnel points to the running `pantas-v2.5` server.

## Recommendation

No backend contract changes are required before continuing the architecture cleanup.

Recommended follow-up work:

- Decide whether mobile registration should submit to the backend or remain a web-only registration flow.
- Decide whether mobile profile should return `qrcode` / `library_qr_code` for a real QR card.
- Change or document `ApiConfig.androidEmulator` before Android emulator testing.
- Keep running `php artisan test --filter=MobileAggregateControllerTest` after mobile API changes.
