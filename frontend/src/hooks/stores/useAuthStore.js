import { create } from "zustand";
import { persist } from "zustand/middleware";

const useAuthStore = create(
    persist(
        (set) => ({
            accessToken: null,
            setAuth: (accessToken) =>
                set({
                    accessToken: accessToken,
                }),
            clearAuth: () =>
                set({
                    accessToken: null,
                }),
        }),
        {
            name: "auth-storage",
            partialize: (state) => ({
                accessToken: state.accessToken,
            }),
        },
    ),
);

export default useAuthStore;
