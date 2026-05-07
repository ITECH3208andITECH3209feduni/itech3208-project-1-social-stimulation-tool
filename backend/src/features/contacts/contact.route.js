import express from "express";
import { bodyMw, authMw } from "#middlewares/index.js";
import ContactController from "./contact.controller.js";
import ContactSchema from "./contact.validation.js";

const router = express.Router();

// POST /api/v1/contacts — Send contact message (Authenticated users)
router.post(
    "/",
    authMw.authorizeRole([authMw.UserRole.user, authMw.UserRole.admin]),
    bodyMw.validate(ContactSchema.sendContact),
    ContactController.sendContact
);

// GET /api/v1/contacts — Get contact messages (Admin only)
router.get(
    "/",
    authMw.authorizeRole([authMw.UserRole.admin]),
    ContactController.getContacts
);

// PATCH /api/v1/contacts/status/:id — Update contact status (Admin only)
router.patch(
    "/status/:id",
    authMw.authorizeRole([authMw.UserRole.admin]),
    bodyMw.validate(ContactSchema.updateStatus),
    ContactController.updateStatus
);

export default router;
