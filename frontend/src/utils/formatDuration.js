/**
 * Formats a video duration value into a human-readable time string.
 *
 * Accepts two input formats:
 * - A number (total seconds): e.g. 3725 → "01:02:05"
 * - A pre-formatted string: e.g. "02:05:30" → "02:05:30" (returned as-is)
 *
 * Output format:
 * - "MM:SS"       when duration is less than 1 hour
 * - "HH:MM:SS"    when duration is 1 hour or more
 *
 * @param {number|string} val - Total seconds (number) or a pre-formatted HH:MM:SS string
 * @returns {string} Formatted duration string, defaults to "00:00" on invalid input
 */
export const formatDuration = (val) => {
    if (!val) return "00:00";

    // If it's already a pre-formatted time string, return it directly
    if (typeof val === "string" && val.includes(":")) return val;

    const totalSeconds = Number(val);
    if (isNaN(totalSeconds)) return "00:00";

    const pad = (n) => String(Math.floor(n)).padStart(2, "0");

    const hours = totalSeconds / 3600;
    const minutes = (totalSeconds % 3600) / 60;
    const seconds = totalSeconds % 60;

    if (Math.floor(hours) > 0) {
        return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
    }

    return `${pad(minutes)}:${pad(seconds)}`;
};
