import express from "express";
import { bodyMw, authMw } from "#middlewares/index.js";
import FeedbackController from "./feedback.controller.js";
import FeedbackSchema from "./feedback.validation.js";

const router = express.Router();
const adminRouter = express.Router();

// POST /api/v1/feedbacks — Create feedback (Authenticated users)
router.post(
    "/",
    authMw.authorizeRole([
        authMw.UserRole.individual,
        authMw.UserRole.organization,
        authMw.UserRole.admin,
    ]),
    bodyMw.validate(FeedbackSchema.createFeedback),
    FeedbackController.createFeedback,
);

// POST /feedback/prompt/dismiss
router.post(
    "/prompt/dismiss",
    authMw.authorizeRole([authMw.UserRole.individual, authMw.UserRole.organization]),
    FeedbackController.dismissPopup,
);

// GET /api/v1/feedbacks/top-feedbacks — Get top feedbacks for landing page (Public)
router.get("/top-feedbacks", FeedbackController.getTopFeedbacks);

// GET /api/v1/feedbacks/:videoId — Get feedbacks for a video (Public)
router.get("/:videoId", FeedbackController.getFeedbacksByVideo);

// DELETE /api/v1/feedbacks/:id — Delete feedback (Owner only)
router.delete(
    "/:id",
    authMw.authorizeRole([
        authMw.UserRole.individual,
        authMw.UserRole.organization,
        authMw.UserRole.admin,
    ]),
    FeedbackController.deleteFeedback,
);

// MARK: - ADMIN ROUTES

// GET /admin/feedbacks — List all feedbacks (paginated, filterable)
adminRouter.get(
    "/",
    authMw.authorizeRole([authMw.UserRole.admin]),
    bodyMw.validate(FeedbackSchema.getFeedbacks),
    FeedbackController.getAllFeedbacks,
);

// PATCH /admin/feedbacks/:id/pin — Toggle isPinned
adminRouter.patch(
    "/:id/pin",
    authMw.authorizeRole([authMw.UserRole.admin]),
    FeedbackController.togglePin,
);

// DELETE /admin/feedbacks/:id — Admin soft-delete any feedback
adminRouter.delete(
    "/:id",
    authMw.authorizeRole([authMw.UserRole.admin]),
    FeedbackController.adminDeleteFeedback,
);

export { router as FeedbackRouter, adminRouter as FeedbackAdminRouter };
