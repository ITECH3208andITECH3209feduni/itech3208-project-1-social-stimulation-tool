import express from "express";
import { authMw } from "#middlewares/index.js";
import WishlistController from "./wishlist.controller.js";

const router = express.Router();

// PATCH /api/v1/wishlists/toggle/:videoId — Toggle video in wishlist
router.patch(
    "/toggle/:videoId",
    authMw.authorizeRole([authMw.UserRole.individual, authMw.UserRole.organization, authMw.UserRole.admin]),
    WishlistController.toggleWishlist
);

// GET /api/v1/wishlists — Get user wishlist
router.get(
    "/",
    authMw.authorizeRole([authMw.UserRole.individual, authMw.UserRole.organization, authMw.UserRole.admin]),
    WishlistController.getWishlist
);

export default router;
