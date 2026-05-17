import { NextResponse, type NextRequest } from "next/server";
import { jwtVerify } from "jose";

const secret = new TextEncoder().encode(process.env.JWT_SECRET ?? "dev-secret-change-me");

export async function middleware(request: NextRequest) {
  const token = request.cookies.get("flores_alesli_token")?.value;
  const pathname = request.nextUrl.pathname;

  if (!token) {
    return NextResponse.redirect(new URL(`/login?next=${pathname}`, request.url));
  }

  try {
    const { payload } = await jwtVerify(token, secret);
    if (pathname.startsWith("/admin") && payload.role !== "admin") {
      return NextResponse.redirect(new URL("/", request.url));
    }
    return NextResponse.next();
  } catch {
    return NextResponse.redirect(new URL(`/login?next=${pathname}`, request.url));
  }
}

export const config = {
  matcher: ["/admin/:path*", "/perfil/:path*", "/pedidos/:path*"]
};
