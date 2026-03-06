import Link from "next/link"
import { MapPin, Phone, Mail, Clock } from "lucide-react"

export function Footer() {
  return (
    <footer className="bg-sidebar text-sidebar-foreground">
      <div className="container mx-auto px-4 py-16">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
          {/* Brand */}
          <div className="space-y-4">
            <h3 className="font-serif text-2xl font-semibold">Eudaimonia</h3>
            <p className="text-sidebar-foreground/70 text-sm leading-relaxed">
              Experience the art of fine dining where every meal is crafted to nourish both body and soul.
            </p>
          </div>

          {/* Quick Links */}
          <div className="space-y-4">
            <h4 className="font-semibold text-sm uppercase tracking-wider text-sidebar-primary">
              Explore
            </h4>
            <nav className="flex flex-col gap-2">
              <Link href="/about" className="text-sidebar-foreground/70 hover:text-sidebar-foreground transition-colors text-sm">
                About Us
              </Link>
              <Link href="/dining-zones" className="text-sidebar-foreground/70 hover:text-sidebar-foreground transition-colors text-sm">
                Dining Zones
              </Link>
              <Link href="/book" className="text-sidebar-foreground/70 hover:text-sidebar-foreground transition-colors text-sm">
                Reservations
              </Link>
              <Link href="/login" className="text-sidebar-foreground/70 hover:text-sidebar-foreground transition-colors text-sm">
                Guest Login
              </Link>
            </nav>
          </div>

          {/* Contact */}
          <div className="space-y-4">
            <h4 className="font-semibold text-sm uppercase tracking-wider text-sidebar-primary">
              Contact
            </h4>
            <div className="space-y-3">
              <div className="flex items-start gap-3 text-sm">
                <MapPin className="h-4 w-4 mt-0.5 text-sidebar-primary shrink-0" />
                <span className="text-sidebar-foreground/70">
                  123 Culinary Avenue<br />
                  New York, NY 10001
                </span>
              </div>
              <div className="flex items-center gap-3 text-sm">
                <Phone className="h-4 w-4 text-sidebar-primary shrink-0" />
                <span className="text-sidebar-foreground/70">(212) 555-0123</span>
              </div>
              <div className="flex items-center gap-3 text-sm">
                <Mail className="h-4 w-4 text-sidebar-primary shrink-0" />
                <span className="text-sidebar-foreground/70">reservations@eudaimonia.com</span>
              </div>
            </div>
          </div>

          {/* Hours */}
          <div className="space-y-4">
            <h4 className="font-semibold text-sm uppercase tracking-wider text-sidebar-primary">
              Hours
            </h4>
            <div className="space-y-2 text-sm">
              <div className="flex items-start gap-3">
                <Clock className="h-4 w-4 mt-0.5 text-sidebar-primary shrink-0" />
                <div className="text-sidebar-foreground/70">
                  <p>Tuesday - Thursday: 5pm - 10pm</p>
                  <p>Friday - Saturday: 5pm - 11pm</p>
                  <p>Sunday: 5pm - 9pm</p>
                  <p className="text-sidebar-foreground/50">Monday: Closed</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="border-t border-sidebar-border mt-12 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
          <p className="text-sidebar-foreground/50 text-sm">
            &copy; {new Date().getFullYear()} Eudaimonia. All rights reserved.
          </p>
          <div className="flex gap-6 text-sm">
            <Link href="#" className="text-sidebar-foreground/50 hover:text-sidebar-foreground transition-colors">
              Privacy Policy
            </Link>
            <Link href="#" className="text-sidebar-foreground/50 hover:text-sidebar-foreground transition-colors">
              Terms of Service
            </Link>
          </div>
        </div>
      </div>
    </footer>
  )
}
