import express from "express";
import { bodyMw, authMw } from "#middlewares/index.js";
import FeedbackController from "./feedback.controller.js";
import FeedbackSchema from "./feedback.validation.js";

const router = express.Router();

// POST /api/v1/feedbacks — Create feedback (Authenticated users)
router.post(
    "/",
    authMw.authorizeRole([authMw.UserRole.user, authMw.UserRole.admin]),
    bodyMw.validate(FeedbackSchema.createFeedback),
    FeedbackController.createFeedback
);

// GET /api/v1/feedbacks/:videoId — Get feedbacks for a video (Public)
router.get("/:videoId", FeedbackController.getFeedbacksByVideo);

// DELETE /api/v1/feedbacks/:id — Delete feedback (Owner only)
router.delete(
    "/:id",
    authMw.authorizeRole([authMw.UserRole.user, authMw.UserRole.admin]),
    FeedbackController.deleteFeedback
);

export default router;
