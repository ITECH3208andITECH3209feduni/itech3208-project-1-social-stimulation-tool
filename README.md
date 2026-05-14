# Social Stimulation Tool

A full-stack web application designed to support social skill development
through video-based learning content. Built for ITECH3208/ITECH3209 at
Federation University.(more details )

---

## What Is This?

The Social Stimulation Tool helps users — individuals and organisations —
discover, watch, and engage with curated video content that supports social
learning. The project is made up of three apps:

- **Backend** — REST API server (Node.js + Express + MongoDB Atlas)
- **Frontend** — User-facing web app (React + Vite)
- **Admin** — Admin dashboard for managing content and users (React + Vite)

---

## Quick Start

### Prerequisites

- Node.js v18+
- MongoDB Atlas account
- Cloudinary account (for image/file uploads)

### Clone & Install

```bash
git clone https://github.com/ITECH3208andITECH3209feduni/itech3208-project-1-social-stimulation-tool.git
cd itech3208-project-1-social-stimulation-tool
```

**Backend**

```bash
cd backend
npm install
cp env/.env.example env/.env   # fill in your values
npm run dev                    # runs on http://localhost:3000
```

**Frontend**

```bash
cd frontend
npm install
cp .env.example .env           # fill in your values
npm run dev                    # runs on http://localhost:5173
```

**Admin**

```bash
cd admin
npm install
cp .env.example .env           # fill in your values
npm run dev                    # runs on http://localhost:5174
```

---

## Environment Variables

### Backend (`backend/env/.env`)

```env
PORT=3000
MONGO_URI=mongodb+srv://<user>:<password>@cluster.mongodb.net/<dbname>
JWT_SECRET=your_jwt_secret

CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret

EMAIL_USER=your_email@example.com
EMAIL_PASS=your_email_password
```

### Frontend & Admin (`.env`)

```env
VITE_API_BASE_URL=http://localhost:3000
```

---

## Project Structure

```
social-stimulation-tool/
│
├── backend/
│   ├── src/
│   │   ├── config/         # DB connection, API paths, env setup
│   │   ├── constants/      # Shared error codes and messages
│   │   ├── features/       # Business logic per feature
│   │   ├── middlewares/    # Auth, validation, error handling
│   │   ├── models/         # Mongoose schemas
│   │   ├── routes/         # API endpoint definitions
│   │   ├── scripts/        # Utility scripts (e.g. seed admin)
│   │   └── utils/          # Logger, response formatter helpers
│   ├── index.js
│   └── package.json
│
├── frontend/               # User-facing app (individual & organization)
│   ├── src/
│   │   ├── assets/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── services/       # Axios API calls
│   │   ├── store/          # Zustand state
│   │   └── utils/
│   └── package.json
│
└── admin/                  # Admin dashboard
    ├── src/
    │   ├── assets/
    │   ├── components/
    │   ├── pages/
    │   ├── services/
    │   ├── store/          # Zustand state
    │   └── utils/
    └── package.json
```

---

## Architecture

````
**Key backend files:**

| File | Purpose |
|---|---|
| `backend/index.js` | Entry point — starts server, wires up routes and middlewares |
| `src/config/index.js` | DB connection, env vars, API path constants |
| `src/routes/index.js` | Exports all routers |
| `src/middlewares/` | JWT auth check, Joi request validation, error handler |
| `src/models/User.js` | User schema — roles: `admin`, `individual`, `organization` |
| `src/features/` | Core logic for auth, videos, categories, wishlist, etc. |

---

## Tech Stack

### Backend

| Library | Purpose |
|---|---|
| Express | Web server and routing |
| Mongoose | MongoDB object modelling |
| JSON Web Token | Authentication tokens |
| bcrypt | Password hashing |
| Joi | Request body validation |
| Multer + Cloudinary | File/image uploads |
| Nodemailer | Sending emails |
| dotenv | Environment variable loading |
| uuid | Unique ID generation |
| http-status-codes | Named HTTP status constants |
| nodemon | Dev auto-restart on file save |

### Frontend (User App)

| Library | Purpose |
|---|---|
| React 19 + Vite | UI framework and build tool |
| React Router DOM v7 | Page routing |
| Zustand | Global state management |
| Chakra UI + Tailwind CSS | Component library and utility styling |
| Axios | HTTP requests to backend |
| React Player | Video playback |
| React Icons | Icon set |
| next-themes | Dark/light mode support |

### Admin Dashboard

| Library | Purpose |
|---|---|
| React 19 + Vite | UI framework and build tool |
| React Router DOM v7 | Page routing |
| Zustand | Global state management |
| Chakra UI + Tailwind CSS | Component library and utility styling |
| jwt-decode | Decode JWT to read role/claims on the client |
| React Player | Video playback |
| React Icons | Icon set |
| next-themes | Dark/light mode support |

> The admin app includes `jwt-decode` to read the user's role from the JWT token client-side — this is used to guard admin-only routes in the UI.

---

## API Overview

All routes are prefixed with the base path set in `src/config/`.

| Endpoint | Description | Auth Required |
|---|---|---|
| `POST /auth/register` | Create a new account | No |
| `POST /auth/login` | Login and receive JWT | No |
| `GET /category` | List all categories | No |
| `GET /level` | List all levels | No |
| `GET /video` | Browse videos | No |
| `POST /feedback` | Submit feedback | No |
| `GET /me` | View own profile | Yes |
| `PUT /me` | Update own profile | Yes |
| `POST /contact` | Send contact message | Yes |
| `GET /individual/wishlists` | Get wishlist | Yes (individual) |
| `POST /individual/wishlists` | Add to wishlist | Yes (individual) |
| `GET /me/videos` | Manage own videos | Yes |
| `GET /manage/category` | Manage categories | Yes (admin) |
| `GET /admin/levels` | Manage levels | Yes (admin) |
| `GET /manage/video` | Manage all videos | Yes (admin) |

---

## User Roles

| Role | Access |
|---|---|
| `individual` | Default role. Browse videos, manage wishlist, edit profile. |
| `organization` | Submit and manage their own video content. |
| `admin` | Full access via the admin dashboard — manage categories, levels, videos, and users. |

> **Creating an admin account:**
> Admin cannot be registered through the public endpoint — this is intentional for security.
> 1. Register a normal user via `POST /auth/register`
> 2. Go to [MongoDB Atlas](https://cloud.mongodb.com) → Collections → `users`
> 3. Find the user and change `role` from `"individual"` to `"admin"`

---

## Available Scripts

**All three apps**

```bash
npm run dev        # start development server
npm run build      # production build
npm run preview    # preview production build locally
npm run format     # format code with Prettier
npm run lint       # lint with ESLint
````

**Backend only**

```bash
npm test           # run tests
npm run allow-ip   # whitelist an IP for dev access
```

---

## FAQ

**Why can't I register as admin through Postman?**

The registration validation only accepts `"individual"` and `"organization"`.
Admin accounts must be manually set in MongoDB Atlas. See
[User Roles](#user-roles).

**I'm getting a `VALIDATION_ERROR` on the `role` field.**

You're likely sending a value that isn't exactly `"individual"` or
`"organization"` (it's case-sensitive). You can also leave `role` out entirely —
it defaults to `"individual"`.

**Where are uploaded images stored?**

Images go to Cloudinary, not the local server. The URL and `cloudinaryId` are
saved in MongoDB on the user or video document.

**How does authentication work?**

Login returns a JWT token. The frontend/admin app stores this and sends it as
`Authorization: Bearer <token>` on every protected request. The admin app also
uses `jwt-decode` to read the role from the token client-side for UI route
guarding.

**How do I connect to MongoDB Atlas?**

Set `MONGO_URI` in `backend/env/.env`. The connection is established in
`src/config/` before the server starts.

---

## Contributing

1. Branch off `development`: `git checkout -b feature/your-feature`
2. Make your changes
3. Run `npm run format` before committing
4. Open a pull request into `development`

Please don't commit directly to `main`.

---

## Changelog

See [CHANGELOG.md](./CHANGELOG.md) for version history and breaking changes.

---

## License

ISC

---

_ITECH3208 / ITECH3209 — Federation University_
