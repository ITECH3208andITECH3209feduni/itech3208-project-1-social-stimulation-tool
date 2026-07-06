import express from "express";
import { bodyMw, authMw } from "#middlewares/index.js";
import ContactController from "./contact.controller.js";
import ContactSchema from "./contact.validation.js";

const router = express.Router();
const adminRouter = express.Router();

// POST /api/v1/contacts — Send contact message (Authenticated users)
router.post(
    "/",
    authMw.authorizeRole([
        authMw.UserRole.individual,
        authMw.UserRole.organization,
        authMw.UserRole.admin,
    ]),
    bodyMw.validate(ContactSchema.sendContact),
    ContactController.sendContact,
);

// PATCH /api/v1/contacts/status/:id — Update contact status (Admin only)
router.patch(
    "/status/:id",
    authMw.authorizeRole([authMw.UserRole.admin]),
    bodyMw.validate(ContactSchema.updateStatus),
    ContactController.updateStatus,
);

// GET /api/v1/contacts — Get contact messages (Admin only)
adminRouter.get("/", authMw.authorizeRole([authMw.UserRole.admin]), ContactController.getContacts);

export { router as ContactPublicRouter, adminRouter as ContactAdminRouter };
