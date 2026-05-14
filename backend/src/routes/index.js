import { CategoryPublicRouter, CategoryAdminRouter } from "#features/categories/category.route.js";
import { LevelPublicRouter, LevelAdminRouter } from "#features/levels/level.route.js";
import AuthRouter from "#features/auth/auth.route.js";
import UserRouter from "#features/users/user.route.js";
import { VideoPublicRouter, VideoUserRouter, VideoAdminRouter } from "#features/videos/video.route.js";
import FeedbackRouter from "#features/feedbacks/feedback.route.js";
import WishlistRouter from "#features/wishlists/wishlist.route.js";
import ContactRouter from "#features/contacts/contact.route.js";

export {
    CategoryPublicRouter,
    CategoryAdminRouter,
    LevelPublicRouter,
    LevelAdminRouter,
    AuthRouter,
    UserRouter,
    VideoPublicRouter,
    VideoUserRouter,
    VideoAdminRouter,
    FeedbackRouter,
    WishlistRouter,
    ContactRouter
};
