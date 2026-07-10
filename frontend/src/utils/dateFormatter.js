/**
 * Standardized date formatting utility
 * Provides consistent date/time formatting across the entire application
 */

/**
 * Format date to standard format: MMM DD, YYYY
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date
 */
export function formatDate(dateString) {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

/**
 * Format date and time to standard format: MMM DD, YYYY at HH:MM AM/PM
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date and time
 */
export function formatDateTime(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const datePart = date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
  const timePart = date.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
  return `${datePart} at ${timePart}`
}

/**
 * Format time only: HH:MM AM/PM
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted time
 */
export function formatTime(dateString) {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

/**
 * Format relative time (e.g., "2 hours ago")
 * @param {string} dateString - ISO date string
 * @returns {string} Relative time string
 */
export function formatRelativeTime(dateString) {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now - date
  const diffMins = Math.floor(diffMs / 60000)
  const diffHours = Math.floor(diffMs / 3600000)
  const diffDays = Math.floor(diffMs / 86400000)

  if (diffMins < 1) return 'Just now'
  if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`
  if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`
  if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`
  return formatDate(dateString)
}
