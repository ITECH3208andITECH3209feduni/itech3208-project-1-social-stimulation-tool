import { CategoryPublicRouter, CategoryAdminRouter } from "#features/categories/category.route.js";
import { SubCategoryPublicRouter, SubCategoryAdminRouter } from "#features/subcategories/subcategory.route.js";
import { LevelPublicRouter, LevelAdminRouter } from "#features/levels/level.route.js";
import AuthRouter from "#features/auth/auth.route.js";
import { UserRouter, UserAdminRouter } from "#features/users/user.route.js";
import {
    VideoPublicRouter,
    VideoUserRouter,
    VideoAdminRouter,
} from "#features/videos/video.route.js";
import FeedbackRouter from "#features/feedbacks/feedback.route.js";
import WishlistRouter from "#features/wishlists/wishlist.route.js";
import { ContactPublicRouter, ContactAdminRouter } from "#features/contacts/contact.route.js";

export {
    CategoryPublicRouter,
    CategoryAdminRouter,
    SubCategoryPublicRouter,
    SubCategoryAdminRouter,
    LevelPublicRouter,
    LevelAdminRouter,
    AuthRouter,
    UserRouter,
    UserAdminRouter,
    VideoPublicRouter,
    VideoUserRouter,
    VideoAdminRouter,
    FeedbackRouter,
    WishlistRouter,
    ContactPublicRouter,
    ContactAdminRouter,
};
