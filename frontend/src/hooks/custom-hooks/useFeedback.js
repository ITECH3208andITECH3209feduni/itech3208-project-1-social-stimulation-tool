import { useRef, useState, useEffect } from "react";
import { feedbackApi } from "@/api";
import { useLocation } from "react-router-dom";
import useUserProfile from "./useUserProfile";
import useAuthStore from "@/hooks/stores/useAuthStore";
import loggerUtil from "@/utils/logger.utils";
import { useQueryClient } from "@tanstack/react-query";

const ONE_MINUTE = 60 * 1000;
const DISMISS_COOLDOWN = 7 * 24 * 60 * 60 * 1000; // 7 days
const SUBMIT_COOLDOWN = 30 * 24 * 60 * 60 * 1000; // 30 days

// MARK: - Do not show popup in these routes
const EXCLUDED_ROUTES = [
    "/account/login",
    "/account/register",
    "/account/update-profile",
    "/account/send-feedback",
];

const useFeedback = () => {
    const queryClient = useQueryClient();

    const { data: user } = useUserProfile();
    const { accessToken } = useAuthStore();
    const location = useLocation();
    const isLoggedIn = !!accessToken;

    const [loading, setLoading] = useState(false);
    const [open, setOpen] = useState(false);
    // Tracks whether the countdown has completed in this session
    const [timerFinished, setTimerFinished] = useState(false);
    // Set to true when user explicitly dismisses or submits in this session
    const isDismissedSession = useRef(false);
    // Tracks which userId the timer was started for, preventing re-run on re-login
    const timerStartedForUser = useRef(null);

    const feedbackMeta = user?.feedbackMeta;

    const isExcludedRoute = EXCLUDED_ROUTES.includes(location.pathname);

    const sendFeedback = async (payload, callback = {}) => {
        try {
            setLoading(true);
            const res = await feedbackApi.createFeedback(payload);
            if (res.success) {
                isDismissedSession.current = true;
                queryClient.invalidateQueries({
                    queryKey: ["user-profile"],
                });
                callback.onSuccess?.(res.data, res.message);
                setLoading(false);
                return true;
            }
        } catch (error) {
            callback.onError?.(error.message);
            setLoading(false);
        } finally {
            callback.onFinally?.();
            setLoading(false);
        }
    };

    const dismisPopup = async () => {
        try {
            isDismissedSession.current = true;
            setOpen(false);
            await feedbackApi.dismissFeedbackPopup();
        } catch (error) {
            loggerUtil.error(`dismisPopup: ${error}`);
        }
    };

    const shouldShowPopup = (meta) => {
        const now = Date.now();

        // 1. User must be logged in
        if (!meta) return false;

        // 2. If user already submitted recently → suppress (do not show popup)
        const submittedRecently = now - new Date(meta.lastSubmittedAt).getTime();
        if (meta.lastSubmittedAt && submittedRecently < SUBMIT_COOLDOWN) {
            return false;
        }

        // 3. If user dismissed recently → suppress (do not show popup)
        const dismissedRecently = now - new Date(meta.lastDismissedAt).getTime();
        if (meta.lastDismissedAt && dismissedRecently < DISMISS_COOLDOWN) {
            return false;
        }

        return true;
    };

    // MARK: - Logout effect
    // Uses accessToken (Zustand) as immediate signal — faster than React Query cache clearing.
    useEffect(() => {
        if (!isLoggedIn) {
            setTimerFinished(false);
            setOpen(false);
            isDismissedSession.current = false;
            timerStartedForUser.current = null;
        }
    }, [isLoggedIn]);

    // MARK: - Timer effect
    // Runs once per login session (keyed by userId). Does NOT depend on route.
    useEffect(() => {
        if (!user || !isLoggedIn) return;

        // Timer already ran for this user in this session, do nothing
        if (timerStartedForUser.current === user._id) return;

        // Backend cooldown says no need to show
        if (!shouldShowPopup(feedbackMeta)) return;

        // Start the countdown once and record which user it belongs to
        timerStartedForUser.current = user._id;
        const timer = setTimeout(() => {
            setTimerFinished(true);
        }, ONE_MINUTE);

        return () => clearTimeout(timer);
    }, [user, isLoggedIn]);

    // MARK: - Visibility effect
    // Controls whether popup is visible based on current route.
    // Does NOT reset the timer, only reacts to show/hide reactively.
    useEffect(() => {
        // Not logged in or already dismissed/submitted this session
        if (!isLoggedIn || isDismissedSession.current) {
            setOpen(false);
            return;
        }

        // Timer not done yet → never show
        if (!timerFinished) {
            setOpen(false);
            return;
        }

        // On excluded route → hide popup but preserve timerFinished
        // so it shows again when user navigates back
        if (isExcludedRoute) {
            setOpen(false);
            return;
        }

        // All conditions passed → show popup
        if (shouldShowPopup(feedbackMeta)) {
            setOpen(true);
        }
    }, [timerFinished, isExcludedRoute, isLoggedIn, feedbackMeta]);

    return { sendFeedback, dismisPopup, setOpen, loading, open };
};

export default useFeedback;
